<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;

// === AUTH ROUTE ===
Route::get('/', Login::class)->name('login');
