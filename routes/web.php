<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Products routes
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::resource('payments', PaymentController::class);
    Route::get('sales/{sale}/payments', [PaymentController::class, 'getPaymentsBySale'])->name('sales.payments');
    Route::get('sales/{sale}/payments/create', [PaymentController::class, 'createPaymentRecord'])->name('sales.payments.create');
    Route::post('sales/{sale}/payments', [PaymentController::class, 'addPaymentRecord'])->name('sales.payments.add');
});

Route::middleware('guest')->group(function () {
    Route::post('/login-attempt', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/', [AuthController::class, 'loginForm'])->name('login');
    Route::get('/password-reset', [PasswordResetController::class, 'passwordResetPage'])->name('password-reset-page');
    Route::post('/reset-link', [PasswordResetController::class, 'sendResetLink'])->name('send-reset-link');
    Route::get('/reset-password-page/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/password-reset-sent', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});