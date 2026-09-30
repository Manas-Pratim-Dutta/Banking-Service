<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ .'/web/applications.php';
require __DIR__ .'/web/edeed.php';