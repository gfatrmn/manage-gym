<?php

use App\Helpers\RouteHelpers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Master Produk (Suplemen & Vitamin)
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/products', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $products = Product::query()
        ->orderByDesc('is_active')
        ->orderBy('category')
        ->orderBy('name')
        ->get();

    return view('admin.products', array_merge(RouteHelpers::pageMeta('products'), [
        'products' => $products
    ]));
})->name('products');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/products', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255'],
        'category'    => ['required', 'in:suplemen,vitamin'],
        'brand'       => ['nullable', 'string', 'max:255'],
        'sku'         => ['nullable', 'string', 'max:80', 'unique:products,sku'],
        'price'       => ['required', 'integer', 'min:1'],
        'stock'       => ['required', 'integer', 'min:0'],
        'unit'        => ['required', 'string', 'max:40'],
        'description' => ['nullable', 'string'],
        'is_active'   => ['nullable', 'boolean'],
    ]);

    $validated['is_active'] = $request->boolean('is_active', true);

    Product::create($validated);

    return redirect()->route('admin.products')->with('status', 'Produk berhasil ditambahkan.');
})->name('products.store');

// ── Update ────────────────────────────────────────────────────────────────────
Route::put('/products/{product}', function (Request $request, Product $product) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255'],
        'category'    => ['required', 'in:suplemen,vitamin'],
        'brand'       => ['nullable', 'string', 'max:255'],
        'sku'         => ['nullable', 'string', 'max:80', 'unique:products,sku,' . $product->id],
        'price'       => ['required', 'integer', 'min:1'],
        'stock'       => ['required', 'integer', 'min:0'],
        'unit'        => ['required', 'string', 'max:40'],
        'description' => ['nullable', 'string'],
        'is_active'   => ['nullable', 'boolean'],
    ]);

    $validated['is_active'] = $request->boolean('is_active');

    $product->update($validated);

    return redirect()->route('admin.products')->with('status', 'Produk berhasil diperbarui.');
})->name('products.update');

// ── Destroy ───────────────────────────────────────────────────────────────────
Route::delete('/products/{product}', function (Product $product) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $product->delete();

    return redirect()->route('admin.products')->with('status', 'Produk berhasil dihapus.');
})->name('products.destroy');
