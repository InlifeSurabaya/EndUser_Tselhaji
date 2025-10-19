<?php

use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Product\IndexProduct;
use App\Livewire\Dashboard;
use App\Livewire\User\UserProfile;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Payment\Detail as DetailPayment;
use App\Livewire\Payment\Create as CreatePayment;


// === AUTH ROUTE ===
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

// === DASHBOARD ROUTE ===
Route::get('/', Dashboard::class)->name('dashboard');

// === PRODUCT ROUTE ===
Route::get('/product', IndexProduct::class)->name('index.product');

// === USER ROUTE ===
Route::get('/user-profile', UserProfile::class)->name('user.profile');


// === ADMIN ROUTE ===
Route::get('/admin', DashboardAdmin::class)->name('admin.dashboard');

// === PAYMENT ROUTE ===
Route::get('/order', DetailPayment::class)->name('payment.detail');
Route::get('/create-order', CreatePayment::class)->name('payment.create');
