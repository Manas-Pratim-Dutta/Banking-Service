<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeedController;
use App\Http\Controllers\DeedRegistrationController;
use App\Http\Controllers\MockNgdrsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// Guest / Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/master', [AuthController::class, 'loginMaster'])->name('login.master');
Route::post('/login/branch', [AuthController::class, 'loginBranch'])->name('login.branch');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Bank Portal Routes
Route::middleware('bank.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches Management
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::post('/branches/{branch}/toggle', [BranchController::class, 'toggleStatus'])->name('branches.toggle');

    // Branch Users (Authorized Officers) Management
    Route::get('/branch-users', [BranchUserController::class, 'index'])->name('branch-users.index');
    Route::post('/branch-users', [BranchUserController::class, 'store'])->name('branch-users.store');
    Route::post('/branch-users/{branchUser}/toggle', [BranchUserController::class, 'toggleStatus'])->name('branch-users.toggle');

    // Deeds (Application Tracking)
    Route::get('/deeds', [DeedController::class, 'index'])->name('deeds.index');
    Route::post('/deeds', [DeedController::class, 'store'])->name('deeds.store');
    Route::get('/deeds/{deed}', [DeedController::class, 'show'])->name('deeds.show');

    // Registration Tracking with Visual Timeline (Matching Screenshot 2)
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    // Instant "+ Initiate registration" without form
    Route::post('/deeds/quick-initiate', [DeedRegistrationController::class, 'quickInitiate'])->name('deeds.quick-initiate');

    // NGDRS SSO Handshake Redirection for specific deed
    Route::post('/deeds/{deed}/sso', [DeedRegistrationController::class, 'initiateSso'])->name('deeds.sso');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Official NGDRS Citizen SSO Endpoint (matching ngdrs_citizen-dev POST /asmsso)
Route::match(['get', 'post'], '/asmsso', [MockNgdrsController::class, 'asmsso'])->name('asmsso');

// Mock Third-Party NGDRS Landing Portal
Route::match(['get', 'post'], '/mock-ngdrs/portal', [MockNgdrsController::class, 'portal'])->name('mock-ngdrs.portal');
Route::post('/mock-ngdrs/update-status', [MockNgdrsController::class, 'updateStatus'])->name('mock-ngdrs.update-status');
