<?php

use App\Http\Controllers\Admin\CashierAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/cashier-accounts', [CashierAccountController::class, 'index'])->name('cashier-accounts');
Route::post('/cashier-accounts', [CashierAccountController::class, 'store'])->name('cashier-accounts.store');
Route::delete('/cashier-accounts/{user}', [CashierAccountController::class, 'destroy'])->name('cashier-accounts.destroy');
