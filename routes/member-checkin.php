<?php

use App\Http\Controllers\Kiosk\MemberCheckinController;
use Illuminate\Support\Facades\Route;

Route::get('/member-checkin', fn () => redirect()->route('login'))->name('member.checkin');
Route::post('/member-checkin', [MemberCheckinController::class, 'store'])->name('member.checkin.store');
