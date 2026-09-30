<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Services\Module\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Check for Holiday / Date availability.
     * POST /api/v1/slots/check-date
     */
    public function checkDate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'        => 'required|string',
            'state_id'    => 'required|numeric',
            'district_id' => 'required|numeric',
        ]);

        $result = $this->appointmentService->checkDateAvailability($validated);

        return response()->json($result);
    }

    /**
     * Fetch available slots from external service.
     * POST /api/v1/slots/available
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'office_id'        => 'required|numeric',
            'shift_id'         => 'required|numeric',
            'appointment_date' => 'required|string',
            'appointment_type' => 'nullable|string',
        ]);

        $result = $this->appointmentService->getAvailableSlots($validated);

        return response()->json($result);
    }

    /**
     * Book citizen appointment via external service.
     * POST /api/v1/appointments/book
     */
    public function bookCitizenAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'                   => 'required|string',
            'office_id'                  => 'required|numeric',
            'shift_id'                   => 'required|numeric',
            'appointment_date'           => 'required|string',
            'interval_id'                => 'required|numeric',
            'slot_no'                    => 'required|numeric',
            'sheduled_time'              => 'required|string',
            'user_id'                    => 'required|numeric',
            'applicant_mobile_numbers'   => 'required|array',
            'applicant_mobile_numbers.*' => 'required|string',
        ]);

        $result = $this->appointmentService->bookAppointment($validated);

        $status = (!empty($result['success'])) ? 200 : 422;

        return response()->json($result, $status);
    }

    /**
     * Book government appointment.
     * POST /api/v1/appointments/govt/book
     */
    public function bookGovtAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'         => 'required|string',
            'office_id'        => 'required|numeric',
            'shift_id'         => 'required|numeric',
            'appointment_date' => 'required|string',
            'interval_id'      => 'required|numeric',
            'slot_no'          => 'required|numeric',
            'sheduled_time'    => 'required|string',
        ]);

        $result = $this->appointmentService->bookGovtAppointment($validated);

        return response()->json($result);
    }

    /**
     * Get appointment status by token number.
     * GET /api/v1/appointments/{token_no}
     */
    public function getAppointmentStatus(Request $request, string $token_no): JsonResponse
    {
        $result = $this->appointmentService->getAppointmentStatus($token_no);

        return response()->json($result);
    }

    /**
     * Cancel appointment by token number.
     * DELETE /api/v1/appointments/{token_no}/cancel
     */
    public function cancelAppointment(Request $request, string $token_no): JsonResponse
    {
        $result = $this->appointmentService->cancelAppointment($token_no);

        return response()->json($result);
    }

    /**
     * Reschedule appointment.
     * POST /api/v1/appointments/reschedule
     */
    public function rescheduleAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token_no'                   => 'required|string',
            'office_id'                  => 'required|numeric',
            'shift_id'                   => 'required|numeric',
            'new_appointment_date'       => 'required|string',
            'interval_id'                => 'required|numeric',
            'slot_no'                    => 'required|numeric',
            'sheduled_time'              => 'required|string',
            'is_home_visit'              => 'sometimes|boolean',
            'applicant_mobile_numbers'   => 'sometimes|array',
            'applicant_mobile_numbers.*' => 'string',
        ]);

        $result = $this->appointmentService->rescheduleAppointment($validated);

        return response()->json($result);
    }

    /**
     * Get office shifts.
     * GET /api/v1/offices/{office_id}/shifts
     */
    public function getOfficeShifts(Request $request, int|string $office_id): JsonResponse
    {
        $result = $this->appointmentService->getOfficeShifts($office_id);

        return response()->json($result);
    }
}
