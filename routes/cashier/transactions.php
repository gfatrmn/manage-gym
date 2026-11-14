<?php

use App\Http\Controllers\Cashier\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
Route::get('/transactions/products', [TransactionController::class, 'products'])->name('transactions.products');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::post('/transactions/register-member', [TransactionController::class, 'registerMember'])->name('transactions.register-member');
Route::get('/transactions/register-member', [TransactionController::class, 'showRegisterMember'])->name('transactions.register-member.form');
