<?php


use App\Http\Controllers\Api\Application\UploadController;

Route::post('/documents/upload',[UploadController::class,'upload']);