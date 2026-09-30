<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Application\PartyController;

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post(
        '/get-party-category',
        [PartyController::class, 'getPartyCategory']
    );
    Route::post(
        '/add-party',
        [PartyController::class, 'addParty']
    );
    Route::post(
        '/get-party-fields',
        [PartyController::class, 'getPartyFields']
    );
    Route::post(
        '/party/save',
        [PartyController::class, 'saveParty']
    )->name('party.save');
    Route::post('/set-exemption-under-88', function (\Illuminate\Http\Request $request) {

        session([
            'exemption_under_88' => $request->input('exemption_under_88')
        ]);

        return response()->json(['success' => true]);

    });

});
