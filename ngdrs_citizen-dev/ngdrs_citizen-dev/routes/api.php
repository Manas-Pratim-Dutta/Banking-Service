<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CitizenentryController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




require __DIR__ .'/api/generalInfo.php';
require __DIR__ . '/api/property.php';
require __DIR__ . '/api/uploadDocument.php';
require __DIR__ . '/api/appointment.php';
require __DIR__ . '/api/party.php';
require __DIR__ . '/api/eDeed.php';
require __DIR__ . '/api/feeCalculation.php';
require __DIR__ . '/api/monolith.php';
