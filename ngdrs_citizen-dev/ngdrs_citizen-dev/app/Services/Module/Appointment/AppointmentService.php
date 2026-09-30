<?php

namespace App\Services\Module\Appointment;

use App\Services\Integrations\Appointment\AppointmentHttpClient;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    protected AppointmentHttpClient $httpClient;

    public function __construct(AppointmentHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Resolve a user-friendly error message from exception.
     */
    protected function resolveErrorMessage(\Throwable $e, string $fallback = 'Unable to connect to appointment services.'): string
    {
        // 1. Connection refused, connection timeout, DNS, or cURL errors
        if (
            $e instanceof ConnectionException ||
            stripos($e->getMessage(), 'Failed to connect') !== false ||
            stripos($e->getMessage(), 'Connection refused') !== false ||
            stripos($e->getMessage(), 'Connection timed out') !== false ||
            stripos($e->getMessage(), 'cURL error 7') !== false ||
            stripos($e->getMessage(), 'cURL error 28') !== false ||
            stripos($e->getMessage(), 'Network is unreachable') !== false
        ) {
            return 'Unable to connect to appointment services. Please verify the service is running or try again later.';
        }

        // 2. Microservice HTTP response with JSON error body
        if ($e instanceof RequestException && $e->response) {
            $body = $e->response->json();
            if (!empty($body['message'])) {
                return (string) $body['message'];
            }
            if (!empty($body['error'])) {
                return (string) $body['error'];
            }
            if (!empty($body['errors']) && is_array($body['errors'])) {
                $first = reset($body['errors']);
                if (is_array($first)) {
                    return (string) reset($first);
                }
                if (is_string($first)) {
                    return $first;
                }
            }
        }

        $rawMessage = $e->getMessage();
        if (
            stripos($rawMessage, 'Failed to connect') !== false ||
            stripos($rawMessage, 'Connection refused') !== false ||
            stripos($rawMessage, 'cURL error') !== false
        ) {
            return 'Unable to connect to appointment services. Please try again later.';
        }

        return !empty($rawMessage) ? $rawMessage : $fallback;
    }

    /**
     * Normalize date string to Y-m-d format.
     */
    protected function normalizeDate(?string $date): string
    {
        if (empty($date)) {
            return Carbon::today()->format('Y-m-d');
        }

        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $date;
        }
    }

    /**
     * Check if appointment date is valid and available (holiday check).
     *
     * @param array $params
     * @return array
     */
    public function checkDateAvailability(array $params): array
    {
        try {
            $payload = [
                'date'        => $this->normalizeDate($params['date'] ?? null),
                'state_id'    => (int) $params['state_id'],
                'district_id' => (int) $params['district_id'],
            ];

            return $this->httpClient->checkDate($payload);
        } catch (\Throwable $e) {
            $errorMessage = $this->resolveErrorMessage($e, 'Unable to verify appointment date.');
            $isHoliday = (stripos($errorMessage, 'holiday') !== false || stripos($errorMessage, 'non-working') !== false);

            Log::error('AppointmentService::checkDateAvailability error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success'    => false,
                'is_holiday' => $isHoliday,
                'message'    => $errorMessage
            ];
        }
    }

    /**
     * Get available appointment slots for office and shift.
     * Enforces legacy checks from slot_alocation.ctp.
     *
     * @param array $params
     * @return array
     */
    public function getAvailableSlots(array $params): array
    {
        try {
            $appointmentDate = $this->normalizeDate($params['appointment_date'] ?? null);
            $officeId = (int) $params['office_id'];

            $payload = [
                'office_id'        => $officeId,
                'shift_id'         => (int) $params['shift_id'],
                'appointment_date' => $appointmentDate,
                'appointment_type' => (string) ($params['appointment_type'] ?? 'N'),
            ];

            $response = $this->httpClient->getAvailableSlots($payload);

            if (!empty($response['success'])) {
                $response = $this->applyLegacySlotRules($response, $appointmentDate, $officeId);
            }

            return $response;
        } catch (\Throwable $e) {
            $errorMessage = $this->resolveErrorMessage($e, 'Unable to connect to appointment services.');
            $isHoliday = (stripos($errorMessage, 'holiday') !== false || stripos($errorMessage, 'non-working') !== false);

            Log::error('AppointmentService::getAvailableSlots error', [
                'params'  => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success'    => false,
                'message'    => $errorMessage,
                'is_holiday' => $isHoliday,
                'intervals'  => [],
                'slots'      => [],
                'data'       => []
            ];
        }
    }

    /**
     * Apply legacy conditions & checks from slot_alocation.ctp.
     *
     * @param array $response
     * @param string $appointmentDate Y-m-d
     * @param int $officeId
     * @return array
     */
    protected function applyLegacySlotRules(array $response, string $appointmentDate, int $officeId): array
    {
        $intervalsList = $response['intervals'] ?? $response['slots'] ?? $response['data'] ?? [];
        if (empty($intervalsList) || !is_array($intervalsList)) {
            return $response;
        }

        $isToday = Carbon::parse($appointmentDate)->isToday();
        $currentTimeStr = Carbon::now()->format('H:i');

        // Check date range for Office 15 special rule (01-10-2025 to 01-10-2027)
        $isOffice15Special = false;
        if ($officeId === 15) {
            $appDt = Carbon::parse($appointmentDate);
            $startDate = Carbon::createFromFormat('d-m-Y', '01-10-2025')->startOfDay();
            $endDate   = Carbon::createFromFormat('d-m-Y', '01-10-2027')->endOfDay();
            $isOffice15Special = $appDt->between($startDate, $endDate);
        }

        $virtualOfficeFlag = $response['virtual_office_flag'] ?? 'N';
        $sroUserCount = (int) ($response['sro_user_count'] ?? 1);

        $totalAvailableIntervals = 0;
        $processedIntervals = [];

        foreach ($intervalsList as $interval) {
            $timeRange = $interval['time_range'] ?? $interval['time'] ?? '';
            $isAvailable = (bool) ($interval['is_available'] ?? true);
            $totalCapacity = (int) ($interval['total_capacity'] ?? $interval['total'] ?? 1);
            $bookedCount = (int) ($interval['booked_count'] ?? 0);
            $availableCount = (int) ($interval['available_count'] ?? $interval['available'] ?? max(0, $totalCapacity - $bookedCount));
            $subSlots = $interval['sub_slots'] ?? [];

            // Condition 1: Same-Day Past Interval Filtering (from slot_alocation.ctp)
            if ($isToday && !empty($timeRange)) {
                $timeParts = explode('-', $timeRange);
                if (!empty($timeParts[0])) {
                    $startTime = Carbon::parse(trim($timeParts[0]))->format('H:i');
                    if (strtotime($startTime) <= strtotime($currentTimeStr)) {
                        $isAvailable = false;
                        $availableCount = 0;
                        $interval['is_expired'] = true;

                        // Mark all sub-slots as unavailable if expired
                        if (!empty($subSlots)) {
                            foreach ($subSlots as &$sSlot) {
                                $sSlot['is_available'] = false;
                            }
                            unset($sSlot);
                        }
                    }
                }
            }

            // Condition 2: Office 15 high-capacity check (2.4 min / slot)
            if ($isOffice15Special && !empty($timeRange) && empty($interval['sub_slots'])) {
                $timeParts = explode('-', $timeRange);
                if (count($timeParts) === 2) {
                    $startMinutes = Carbon::parse(trim($timeParts[0]))->minute + (Carbon::parse(trim($timeParts[0]))->hour * 60);
                    $endMinutes = Carbon::parse(trim($timeParts[1]))->minute + (Carbon::parse(trim($timeParts[1]))->hour * 60);
                    $totMinute = abs($endMinutes - $startMinutes);
                    if ($totMinute > 0) {
                        $totalCapacity = (int) floor($totMinute / 2.4);
                        if (!($interval['is_expired'] ?? false)) {
                            $availableCount = max(0, $totalCapacity - $bookedCount);
                            $isAvailable = $availableCount > 0;
                        }
                    }
                }
            }

            // Condition 3: Virtual office scaling
            if ($virtualOfficeFlag === 'Y' && $sroUserCount > 1 && empty($interval['sub_slots'])) {
                $totalCapacity = (int) floor($totalCapacity / $sroUserCount);
                if (!($interval['is_expired'] ?? false)) {
                    $availableCount = max(0, $totalCapacity - $bookedCount);
                    $isAvailable = $availableCount > 0;
                }
            }

            $interval['is_available'] = $isAvailable && ($availableCount > 0);
            $interval['available_count'] = $availableCount;
            $interval['total_capacity'] = $totalCapacity;
            if (!empty($subSlots)) {
                $interval['sub_slots'] = $subSlots;
            }

            if ($interval['is_available']) {
                $totalAvailableIntervals++;
            }

            $processedIntervals[] = $interval;
        }

        $response['intervals'] = $processedIntervals;
        $response['slots'] = $processedIntervals;
        $response['data'] = $processedIntervals;
        $response['available_intervals_count'] = $totalAvailableIntervals;

        return $response;
    }

    /**
     * Book citizen appointment.
     *
     * @param array $params
     * @return array
     */
    public function bookAppointment(array $params): array
    {
        try {
            $payload = [
                'token_no'                 => (string) $params['token_no'],
                'office_id'                => (int) $params['office_id'],
                'shift_id'                 => (int) $params['shift_id'],
                'appointment_date'         => $this->normalizeDate($params['appointment_date'] ?? null),
                'interval_id'              => (int) $params['interval_id'],
                'slot_no'                  => (int) $params['slot_no'],
                'sheduled_time'            => (string) $params['sheduled_time'],
                'user_id'                  => (int) $params['user_id'],
                'applicant_mobile_numbers' => array_values(array_map('strval', (array) ($params['applicant_mobile_numbers'] ?? []))),
            ];

            return $this->httpClient->bookCitizenAppointment($payload);
        } catch (\Throwable $e) {
            $errorMessage = $this->resolveErrorMessage($e, 'Unable to connect to appointment services.');

            Log::error('AppointmentService::bookAppointment error', [
                'payload' => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Book government appointment.
     *
     * @param array $params
     * @return array
     */
    public function bookGovtAppointment(array $params): array
    {
        try {
            $payload = [
                'token_no'         => (string) $params['token_no'],
                'office_id'        => (int) $params['office_id'],
                'shift_id'         => (int) $params['shift_id'],
                'appointment_date' => $this->normalizeDate($params['appointment_date'] ?? null),
                'interval_id'      => (int) $params['interval_id'],
                'slot_no'          => (int) $params['slot_no'],
                'sheduled_time'    => (string) $params['sheduled_time'],
            ];

            return $this->httpClient->bookGovtAppointment($payload);
        } catch (\Throwable $e) {
            $errorMessage = $this->resolveErrorMessage($e, 'Unable to connect to appointment services.');

            Log::error('AppointmentService::bookGovtAppointment error', [
                'payload' => $params,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Check appointment status.
     */
    public function getAppointmentStatus(string $tokenNo): array
    {
        try {
            return $this->httpClient->getAppointmentStatus($tokenNo);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e, 'Unable to connect to appointment services.')
            ];
        }
    }

    /**
     * Cancel appointment.
     */
    public function cancelAppointment(string $tokenNo): array
    {
        try {
            return $this->httpClient->cancelAppointment($tokenNo);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e, 'Unable to connect to appointment services.')
            ];
        }
    }

    /**
     * Reschedule appointment.
     */
    public function rescheduleAppointment(array $params): array
    {
        try {
            $payload = [
                'token_no'                 => (string) $params['token_no'],
                'office_id'                => (int) $params['office_id'],
                'shift_id'                 => (int) $params['shift_id'],
                'new_appointment_date'     => $this->normalizeDate($params['new_appointment_date'] ?? null),
                'interval_id'              => (int) $params['interval_id'],
                'slot_no'                  => (int) $params['slot_no'],
                'sheduled_time'            => (string) $params['sheduled_time'],
                'is_home_visit'            => (bool) ($params['is_home_visit'] ?? false),
                'applicant_mobile_numbers' => array_values(array_map('strval', (array) ($params['applicant_mobile_numbers'] ?? []))),
            ];

            return $this->httpClient->rescheduleAppointment($payload);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e, 'Unable to connect to appointment services.')
            ];
        }
    }

    /**
     * Get office shifts.
     */
    public function getOfficeShifts(int|string $officeId): array
    {
        try {
            return $this->httpClient->getOfficeShifts($officeId);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $this->resolveErrorMessage($e, 'Unable to connect to appointment services.')
            ];
        }
    }
}
