<?php

use App\Http\Controllers\Api\Application\AppointmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Appointment Service API Endpoints
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Slots endpoints
    Route::prefix('slots')->group(function () {
        Route::post('check-date', [AppointmentController::class, 'checkDate']);
        Route::post('available', [AppointmentController::class, 'getAvailableSlots']);
    });

    // Appointments endpoints
    Route::prefix('appointments')->group(function () {
        Route::post('book', [AppointmentController::class, 'bookCitizenAppointment']);
        Route::post('govt/book', [AppointmentController::class, 'bookGovtAppointment']);
        Route::post('reschedule', [AppointmentController::class, 'rescheduleAppointment']);
        Route::get('{token_no}', [AppointmentController::class, 'getAppointmentStatus']);
        Route::delete('{token_no}/cancel', [AppointmentController::class, 'cancelAppointment']);
    });

    // Offices endpoints
    Route::get('offices/{office_id}/shifts', [AppointmentController::class, 'getOfficeShifts']);
});
