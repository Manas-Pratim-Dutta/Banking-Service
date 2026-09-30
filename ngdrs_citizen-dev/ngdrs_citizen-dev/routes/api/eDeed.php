<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Application\EDeedPreviewController;
use App\Http\Controllers\Web\Application\EDeedController;
Route::middleware(['web', 'auth:web'])->group(function () {
   Route::post('/edeed/generate', [EDeedPreviewController::class, 'generate'])
    ->name('edeed.generate');

   
});
