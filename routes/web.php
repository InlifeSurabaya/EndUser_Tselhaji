<?php

use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use App\Livewire\Order\CheckOrder;
use App\Livewire\Order\Create as OrderCreate;
use App\Livewire\Order\Detail as OrderDetail;
use App\Livewire\Product\IndexProduct;
use App\Livewire\User\UserProfile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Transaction\HistoryTransaction;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Admin\ManajemenQris;
use App\Livewire\Admin\ManajemenProduk;

// === AUTH ROUTE ===
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/verify-email', VerifyEmail::class)->name('verification.notice');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

// === DASHBOARD ROUTE ===
Route::get('/', Dashboard::class)->name('dashboard');

// === PRODUCT ROUTE ===
Route::get('/product', IndexProduct::class)->name('index.product');

// === USER ROUTE ===
Route::get('/user-profile', UserProfile::class)->name('user.profile');


// === ADMIN ROUTE ===
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/admin', DashboardAdmin::class)->name('admin.dashboard');
    Route::get('/manajemen-qris', ManajemenQris::class)->name('admin.manajemen-qris');
    Route::get('/manajemen-produk', ManajemenProduk::class)->name('admin.manajemen-produk');
});

// === PAYMENT ROUTE ===

// === ORDER ROUTE ===
Route::get('/order', OrderCreate::class)->name('order.create');
Route::get('/order/{uuidOrder}', OrderDetail::class)->name('order.detail');
Route::get('/check-order', CheckOrder::class)->name('order.check');

// === AUTH ROUTE ===
Route::middleware(
    [
        'auth',
        'role:' . \App\Enum\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enum\RoleEnum::USER->value
    ])
    ->group(function () {
        Route::get('/history', HistoryTransaction::class)->name('transaction.history');
    });
