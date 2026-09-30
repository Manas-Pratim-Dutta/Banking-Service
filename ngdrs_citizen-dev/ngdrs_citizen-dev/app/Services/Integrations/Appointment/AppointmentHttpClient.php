<?php

namespace App\Services\Integrations\Appointment;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppointmentHttpClient
{
    protected string $baseUrl;

    protected int $timeout;

    protected ?string $serviceToken;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.appointment.url', env('APPOINTMENT_SERVICE_URL', 'http://127.0.0.1:7777')), '/');
        $this->timeout = (int) config('services.appointment.timeout', 10);
        $this->serviceToken = config(
            'services.appointment.token',
            env('APPOINTMENT_SERVICE_TOKEN', env('APPOINTMENT_SERVICE_SECRET', env('APPOINTMENT_SERVICE_KEY')))
        );
    }

    /**
     * Build base HTTP client request.
     */
    protected function client(): PendingRequest
    {
        $token = $this->serviceToken ?? config(
            'services.appointment.token',
            env('APPOINTMENT_SERVICE_TOKEN', env('APPOINTMENT_SERVICE_SECRET', env('APPOINTMENT_SERVICE_KEY')))
        );

        $headers = [];
        if ($token !== null && $token !== '') {
            $headers['X-Service-Token'] = (string) $token;
        }

        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withOptions([
                'verify' => true,
                'connect_timeout' => 3,
            ])
            ->withHeaders($headers)
            ->acceptJson();
    }

    /**
     * Check for Holiday / Date availability.
     * Endpoint: POST /api/v1/slots/check-date
     */
    public function checkDate(array $params): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/slots/check-date', $params)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment checkDate failed', [
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Fetch available slots for an office shift and date.
     * Endpoint: POST /api/v1/slots/available
     */
    public function getAvailableSlots(array $params): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/slots/available', $params)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment getAvailableSlots failed', [
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Book citizen appointment.
     * Endpoint: POST /api/v1/appointments/book
     */
    public function bookCitizenAppointment(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/appointments/book', $payload)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment bookCitizenAppointment failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Book government appointment.
     * Endpoint: POST /api/v1/appointments/govt/book
     */
    public function bookGovtAppointment(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/appointments/govt/book', $payload)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment bookGovtAppointment failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get office shifts.
     * Endpoint: GET /api/v1/offices/{office_id}/shifts
     */
    public function getOfficeShifts(int|string $officeId): array
    {
        try {
            $response = $this->client()
                ->get("/api/v1/offices/{$officeId}/shifts")
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment getOfficeShifts failed', [
                'office_id' => $officeId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check appointment status by token.
     * Endpoint: GET /api/v1/appointments/{token_no}
     */
    public function getAppointmentStatus(string $tokenNo): array
    {
        try {
            $response = $this->client()
                ->get("/api/v1/appointments/{$tokenNo}")
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment getAppointmentStatus failed', [
                'token_no' => $tokenNo,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Cancel appointment by token.
     * Endpoint: DELETE /api/v1/appointments/{token_no}/cancel
     */
    public function cancelAppointment(string $tokenNo): array
    {
        try {
            $response = $this->client()
                ->delete("/api/v1/appointments/{$tokenNo}/cancel")
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment cancelAppointment failed', [
                'token_no' => $tokenNo,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reschedule citizen appointment.
     * Endpoint: POST /api/v1/appointments/reschedule
     */
    public function rescheduleAppointment(array $payload): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/appointments/reschedule', $payload)
                ->throw();

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('External Appointment rescheduleAppointment failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
