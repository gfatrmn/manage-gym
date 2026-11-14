<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'root']);

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'submitLogin'])->name('login.submit');

Route::get('/login/member', fn () => redirect()->route('login'))->name('login.member');
Route::get('/login/admin', fn () => redirect()->route('login'))->name('admin.login');
Route::post('/login/admin', [LoginController::class, 'submitLogin'])->name('admin.login.submit');

Route::get('/login/cashier', fn () => redirect()->route('login'))->name('cashier.login');
Route::post('/login/cashier', [LoginController::class, 'submitLogin'])->name('cashier.login.submit');

Route::get('/login/master-admin', fn () => redirect()->route('login'))->name('master-admin.login');
Route::post('/login/master-admin', [LoginController::class, 'submitLogin'])->name('master-admin.login.submit');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
