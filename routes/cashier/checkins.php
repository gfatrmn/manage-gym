<?php

use App\Http\Controllers\Cashier\CheckinController;
use Illuminate\Support\Facades\Route;

Route::get('/checkins', [CheckinController::class, 'index'])->name('checkins');
Route::get('/checkins/lookup-member', [CheckinController::class, 'lookupMember'])->name('checkins.lookup-member');
Route::post('/checkins', [CheckinController::class, 'store'])->name('checkins.store');
Route::post('/checkins/{checkin}/verify', [CheckinController::class, 'verify'])->name('checkins.verify');
Route::post('/checkins/{checkin}/reject', [CheckinController::class, 'reject'])->name('checkins.reject');
