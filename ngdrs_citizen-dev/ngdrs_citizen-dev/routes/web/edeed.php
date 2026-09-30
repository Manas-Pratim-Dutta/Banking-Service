<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Application\EdeedController;
Route::middleware(['auth'])->group(function () {
    Route::get("/edeed", [EdeedController::class, "index"])->name("edeed");
});
