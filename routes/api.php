<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;


Route::controller(TransactionController::class)->group(function () {
   Route::get('/transaction', 'getAllTransaction');
   Route::post('/update-status', 'updateStatusPesanan');
});

use App\Http\Controllers\TestPackageController;

// Routes untuk package analysis
Route::prefix('test')->group(function () {
    Route::post('/upload', [TestPackageController::class, 'testUpload']);
    Route::post('/preview', [TestPackageController::class, 'previewData']);
});
