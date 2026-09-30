<?php

use App\Http\Controllers\Api\MonolithMasterDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Monolith Master Data REST Endpoints (Consumed by Fee Service)
|--------------------------------------------------------------------------
*/

Route::prefix('v1/monolith')->group(function () {
    Route::get('articles/{article_id}', [MonolithMasterDataController::class, 'getArticle']);
    Route::get('villages/{village_id}', [MonolithMasterDataController::class, 'getVillage']);
    Route::get('bool-info/{reginfo_id}', [MonolithMasterDataController::class, 'getBoolInfo']);
    Route::get('developed-land-types/{id}', [MonolithMasterDataController::class, 'getDevelopedLandType']);
});
