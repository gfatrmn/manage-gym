<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::post('/categories', [ProductController::class, 'storeCategory'])->name('categories.store');
Route::put('/categories/{category}', [ProductController::class, 'updateCategory'])->name('categories.update');
Route::delete('/categories/{category}', [ProductController::class, 'destroyCategory'])->name('categories.destroy');
