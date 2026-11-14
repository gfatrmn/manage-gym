<?php

use App\Http\Controllers\Cashier\PackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
