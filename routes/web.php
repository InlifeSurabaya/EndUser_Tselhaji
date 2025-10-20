<?php

use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Product\IndexProduct;
use App\Livewire\Dashboard;
use App\Livewire\User\UserProfile;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Order\Create as OrderCreate;
use App\Livewire\Order\Detail as OrderDetail;
use App\Livewire\Order\CheckOrder;

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

// === ORDER ROUTE ===
Route::get('/order', OrderCreate::class)->name('order.create');
Route::get('/order/{uuidOrder}', OrderDetail::class)->name('order.detail');
Route::get('/check-order', CheckOrder::class)->name('order.check');

