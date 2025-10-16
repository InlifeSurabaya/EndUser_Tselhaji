<?php

use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Product\IndexProduct;

// === AUTH ROUTE ===
Route::get('/', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// === INDEX ROUTE ===


// === PRODUCT ROUTE ===
Route::get('/product', IndexProduct::class)->name('index.product');
