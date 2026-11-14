<?php

use App\Http\Controllers\Admin\MembershipController;
use Illuminate\Support\Facades\Route;

Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
Route::patch('/memberships/{membership}/status', [MembershipController::class, 'updateStatus'])->name('memberships.status');
Route::patch('/memberships/{membership}/update-status', [MembershipController::class, 'updateStatus'])->name('memberships.update-status');
