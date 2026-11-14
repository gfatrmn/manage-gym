<?php

use App\Http\Controllers\Admin\PackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
Route::patch('/packages/{package}/toggle', [PackageController::class, 'toggle'])->name('packages.toggle');
Route::patch('/packages/{package}/toggle-status', [PackageController::class, 'toggle'])->name('packages.toggle-status');
Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
