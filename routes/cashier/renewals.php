<?php

use App\Http\Controllers\Cashier\MemberPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/renewals', [MemberPaymentController::class, 'index'])->name('renewals.index');
Route::post('/renewals', [MemberPaymentController::class, 'store'])->name('renewals.store');
