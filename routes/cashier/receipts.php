<?php

use App\Http\Controllers\Cashier\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/verifications', [ReceiptController::class, 'verifications'])->name('verifications');
Route::post('/verifications/{paymentId}', [ReceiptController::class, 'confirmVerification'])->name('verifications.confirm');
Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts');
Route::get('/receipts/{invoice}/print', [ReceiptController::class, 'printReceipt'])->name('receipts.print');
