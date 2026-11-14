<?php

use App\Http\Controllers\Cashier\DailyPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/daily-payments', [DailyPaymentController::class, 'index'])->name('daily-payments');
Route::post('/daily-payments', [DailyPaymentController::class, 'store'])->name('daily-payments.store');
