<?php

use App\Http\Controllers\Admin\ProfilePhotoRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/profile-photo-requests', [ProfilePhotoRequestController::class, 'index'])->name('profile-photo-requests');
Route::get('/profile-photo-requests/{photoRequest}/photo', [ProfilePhotoRequestController::class, 'showPhoto'])->name('profile-photo-requests.photo');
Route::post('/profile-photo-requests/{photoRequest}/approve', [ProfilePhotoRequestController::class, 'approve'])->name('profile-photo-requests.approve');
Route::post('/profile-photo-requests/{photoRequest}/reject', [ProfilePhotoRequestController::class, 'reject'])->name('profile-photo-requests.reject');
