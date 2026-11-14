<?php

use App\Http\Controllers\Cashier\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/members', [MemberController::class, 'index'])->name('members.index');
