<?php

use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
Route::post('/announcements/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
Route::post('/announcements/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
Route::post('/announcements/restore', [AnnouncementController::class, 'restore'])->name('announcements.restore');
Route::post('/announcements/delete', [AnnouncementController::class, 'destroy'])->name('announcements.delete');
Route::post('/announcements/reminders', [AnnouncementController::class, 'sendReminder'])->name('announcements.reminders.send');
