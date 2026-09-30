<?php

use App\Http\Controllers\Api\Application\PropertyController;
use App\Http\Controllers\Api\Application\ChatController;

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::get('/property-type-change', [PropertyController::class, 'propertyTypeChange']);
    Route::get('/fetch-subdivisions', [PropertyController::class, 'fetchSubdivisions']);
    Route::get('/fetch-circle', [PropertyController::class, 'fetchCircle']);
    Route::get('/fetch-mouja', [PropertyController::class, 'fetchMouja']);
    Route::get('/fetch-village', [PropertyController::class, 'fetchVillage']);
    Route::get('/fetch-area-type', [PropertyController::class, 'fetchAreaType']);
    Route::get('/fetch-noc-details', [PropertyController::class, 'fetchNocDetails']);
    Route::get('/verify-dag', [PropertyController::class, 'verifyDag']);
    Route::get('/get-saved-property', [PropertyController::class, 'getSavedProperty']);
    Route::get('/get-dag-details', [PropertyController::class, 'getDagDetails']);

    Route::get('/chat', [ChatController::class, 'send']);

    Route::post(
        '/property/load-noc-dags',
        [PropertyController::class, 'loadNocDags']
    );

    Route::post(
        '/property/load-manual-dag',
        [PropertyController::class, 'loadManualDag']
    );
    Route::post('/save-property',[PropertyController::class, 'saveProperty']);
    Route::post('/save-manual-property',[PropertyController::class, 'saveManualProperty']);
});