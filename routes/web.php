<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/template', function () {
        return view('pages.template');
    })->name('template');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Products routes
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);
});

Route::post('/login-attempt', [AuthController::class, 'login'])->middleware('guest')->name('login.submit');
Route::get('/', [AuthController::class, 'loginForm'])->middleware('guest')->name('login');