<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use App\Models\OfficeShift;
// use App\Module\Services\AppointmentService as ServicesAppointmentService;
use App\Services\Module\Appointment\AppointmentService as ServicesAppointmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Module\Appointment\AppointmentService;

class AppointmentController extends Controller
{
    
    public function index()
    {
        $preRegNo = session('token_no', '202400021456');
        // dd(session('token_no'));
        $officeId = 1;

        $shiftId = 1;
        $preRegNo = session('token_no', '202500021456');
        return view('pages.appointment', compact(
            'preRegNo',
            'officeId',
            'shiftId'
        ));
    }
}
