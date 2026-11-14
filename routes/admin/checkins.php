<?php

use App\Http\Controllers\Admin\CheckinController;
use Illuminate\Support\Facades\Route;

Route::get('/checkins', [CheckinController::class, 'index'])->name('checkins');
Route::post('/checkins', [CheckinController::class, 'store'])->name('checkins.store');
Route::post('/checkins/daily-pass', [CheckinController::class, 'storeDailyPass'])->name('checkins.daily-pass.store');
