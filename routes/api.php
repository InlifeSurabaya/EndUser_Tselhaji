<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebHookController;

Route::post('/midtrans/webhook', [MidtransWebHookController::class, 'handle'])->name('midtrans.webhook');
