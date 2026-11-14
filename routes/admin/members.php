<?php

use App\Http\Controllers\Admin\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/members', [MemberController::class, 'index'])->name('members');
Route::post('/members', [MemberController::class, 'store'])->name('members.store');
Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
