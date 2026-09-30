<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\Web\Application\ApplicationController;
use App\Http\Controllers\Web\Application\AppointmentController;
use App\Http\Controllers\Web\Application\FeeCalculationController;
use App\Http\Controllers\Web\Application\GeneralInfoController;
use App\Http\Controllers\Web\Application\IdentifierController;
use App\Http\Controllers\Web\Application\PartyController;
use App\Http\Controllers\Web\Application\PropertyController;
use App\Http\Controllers\Web\Application\UploadController;
use App\Http\Controllers\Web\Application\WitnessController;
use App\Http\Controllers\Web\Application\EsignController;
use App\Http\Controllers\Web\Application\EDeedController;
use Illuminate\Support\Facades\Route;

Route::post('/asmsso', [LoginController::class, 'asmsso'])->name('asmsso');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//Temporary route
Route::get('/autoLogin', [LoginController::class, 'autoLogin'])->name('autoLogin');
Route::post(
    '/process-ekyc-response',
    [PartyController::class, 'processEKYCResponse']
)->name('process.ekyc.response');
Route::post(
    '/esign/response',
    [EsignController::class, 'response']
)
    ->name('esign.response');
Route::delete('/edeed/draft/delete', [EDeedController::class, 'destroy'])
    ->name('edeed.draft.destroy');

Route::middleware(['auth'])->group(function () {
    Route::get('/all-applications', [ApplicationController::class, 'index'])->name('all-applications');

    Route::get('/general-info', [GeneralInfoController::class, 'index'])->name('general-info');
    Route::post('/general-info', [GeneralInfoController::class, 'store'])
        ->name('general-info');

    Route::get('/property', [PropertyController::class, 'index'])->name('property');
    Route::get('/get-saved-property', [PropertyController::class, 'getSavedProperty']);
    Route::get('/party', [PartyController::class, 'index'])->name('party');
    Route::get('/witness', [WitnessController::class, 'index'])->name('witness');
    Route::get('/identifier', [IdentifierController::class, 'index'])->name('identifier');
    Route::get('/fee-calculation', [FeeCalculationController::class, 'index'])->name('fee-calculation');
    Route::get('/upload-document', [UploadController::class, 'index'])->name('upload-document');
    Route::get('/documents/view/{id}', [UploadController::class, 'viewFile'])->name('documents.view');
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment');
    Route::get('/edeed/download', [EDeedController::class, 'download'])
        ->name('edeed.download');
    Route::delete('/party/{id}', [PartyController::class, 'delete'])
        ->name('party.delete');
    Route::delete('/identifier/{id}', [IdentifierController::class, 'delete'])
        ->name('identifier.delete');
    Route::delete('/witness/{id}', [WitnessController::class, 'delete'])
        ->name('witness.delete');
    Route::get(
        '/edeed/article-fields',
        [EDeedController::class, 'articleFields']
    )->name('edeed.article-fields');
    Route::post(
        '/edeed/article-details',
        [EDeedController::class, 'saveArticleDetails']
    )->name('edeed.article-details');
    Route::post(
        '/edeed/delete-article-details',
        [EDeedController::class, 'deleteArticleDetails']
    )->name('edeed.delete-article-details');
    Route::get(
        '/redirection-portal',
        [PartyController::class, 'redirectionPortal']
    )->name('redirection.portal');
    Route::post(
        '/add-witness',
        [WitnessController::class, 'addWitness']
    )->name('add.witness');
    Route::post('/save-witness', [WitnessController::class, 'saveWitness'])
        ->name('save.witness');
    Route::post(
        '/add-identifier',
        [IdentifierController::class, 'addIdentifier']
    )->name('add.identifier');

    Route::post(
        '/save-identifier',
        [IdentifierController::class, 'saveIdentifier']
    )->name('save.identifier');

    Route::get(
        '/identifier-ekyc',
        [IdentifierController::class, 'ekyc']
    )->name('identifier.ekyc');

    Route::post(
        '/process-identifier-ekyc-response',
        [IdentifierController::class, 'processEkycResponse']
    )->name('identifier.process.ekyc.response');
    Route::post(
        '/esign/start',
        [EsignController::class, 'start']
    )
        ->name('esign.start');

    Route::post(
        '/party/set-presenter',
        [PartyController::class, 'setPresenter']
    )->name('party.set-presenter');
    Route::post('/party/upload-file', [PartyController::class, 'uploadFile'])
        ->name('party.upload-file');





    Route::get(
        '/esign/pdf/{file}',
        [EsignController::class, 'servePdf']
    )
        ->name('esign.pdf');
    Route::post(
        '/esign/index-new',
        [EsignController::class, 'indexNew']
    )->name('esign.index_new');

});
