<?php

use App\Http\Controllers\Admin\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks');
Route::post('/feedbacks/{feedback}/read', [FeedbackController::class, 'markRead'])->name('feedbacks.read');
Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('feedbacks.destroy');
