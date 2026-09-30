<?php

// use App\Http\Controllers\Web\Application\GeneralInfoController;
use App\Http\Controllers\Api\Application\GeneralInfoController;

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::get('/document-title/{articleId}', [GeneralInfoController::class, 'getTitle']);

    Route::get('/get-talukas/{districtId}', [GeneralInfoController::class, 'getTalukas']);
    Route::get('/get-offices/{talukaId}', [GeneralInfoController::class, 'getOffices']);
});
