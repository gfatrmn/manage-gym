<?php

use App\Http\Controllers\Member\MemberAuthController;
use App\Http\Controllers\Member\MemberPortalController;
use Illuminate\Support\Facades\Route;

// Auth & Activation (Guest / Public Member Routes)
Route::get('/member/login', [MemberAuthController::class, 'showLogin'])->name('member.login');
Route::post('/member/login', [MemberAuthController::class, 'submitLogin'])->name('member.login.submit');

Route::get('/member/activate', fn () => redirect()->route('login'))->name('member.activate.show');
Route::post('/member/activate', fn () => redirect()->route('login'))->name('member.activate.store');

Route::get('/forgot-password', [MemberAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [MemberAuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [MemberAuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [MemberAuthController::class, 'resetPassword'])->name('password.update');

// Authenticated Member Portal Routes
Route::prefix('member')->name('member.')->middleware('role:member')->group(function () {
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/statistics', [MemberPortalController::class, 'statistics'])->name('statistics');
    Route::get('/messages', [MemberPortalController::class, 'messages'])->name('messages');
    Route::get('/history', [MemberPortalController::class, 'history'])->name('history');
    Route::get('/barcode', [MemberPortalController::class, 'barcode'])->name('barcode');
    Route::get('/membership', [MemberPortalController::class, 'membership'])->name('membership');
    Route::get('/packages', [MemberPortalController::class, 'packages'])->name('packages');
    Route::get('/payments', [MemberPortalController::class, 'payments'])->name('payments');

    Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [MemberPortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [MemberPortalController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/password', [MemberPortalController::class, 'updatePassword'])->name('password.change');

    Route::post('/feedback', [MemberPortalController::class, 'submitFeedback'])->name('feedback.submit');
    Route::post('/logout', [MemberPortalController::class, 'logout'])->name('logout');
});
