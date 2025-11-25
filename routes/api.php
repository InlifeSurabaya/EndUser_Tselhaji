<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;


Route::controller(TransactionController::class)->group(function () {
   Route::get('/transaction', 'getAllTransaction');
   Route::post('/update-status', 'updateStatusPesanan');
});
