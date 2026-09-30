<?php

use App\Http\Controllers\Api\Application\FeeCalculationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Fee Calculation Service API Endpoints
|--------------------------------------------------------------------------
*/

Route::prefix('v1/fee-calculation')->group(function () {
    Route::post('rules', [FeeCalculationController::class, 'getFeeRuleChecklist']);
    Route::post('rule-items', [FeeCalculationController::class, 'getFeeRuleItems']);
    Route::match(['get', 'post'], 'valuation', [FeeCalculationController::class, 'getZonalValuation']);
    Route::post('calculate', [FeeCalculationController::class, 'calculateFees']);
    Route::post('calculate-mv', [FeeCalculationController::class, 'calculateMarketValue']);
    Route::post('exemptions/ex-serviceman', [FeeCalculationController::class, 'applyExServicemenExemption']);
    Route::delete('exemptions', [FeeCalculationController::class, 'removeExemption']);
});
