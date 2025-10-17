<?php

use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Product\IndexProduct;
use App\Livewire\Dashboard;


// === AUTH ROUTE ===
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// === DASHBOARD ROUTE ===
Route::get('/', Dashboard::class)->name('dashboard');

// === PRODUCT ROUTE ===
Route::get('/product', IndexProduct::class)->name('index.product');
