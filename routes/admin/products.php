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

    $categories = \App\Models\Category::orderBy('name')->get();

    return view('admin.products', array_merge(RouteHelpers::pageMeta('products'), [
        'products' => $products,
        'categories' => $categories
    ]));
})->name('products');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/products', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255'],
        'category_id' => ['nullable', 'exists:categories,id'],
        'category'    => ['nullable', 'string', 'max:255'],
        'brand'       => ['nullable', 'string', 'max:255'],
        'sku'         => ['nullable', 'string', 'max:80', 'unique:products,sku'],
        'price'       => ['required', 'integer', 'min:1'],
        'stock'       => ['required', 'integer', 'min:0'],
        'unit'        => ['required', 'string', 'max:40'],
        'description' => ['nullable', 'string'],
        'is_active'   => ['nullable', 'boolean'],
    ]);

    if (empty($validated['category_id'])) {
        if (empty($validated['category'])) {
            return back()->withErrors(['category_id' => 'Kategori wajib dipilih.'])->withInput();
        }
        $cat = \App\Models\Category::firstOrCreate([
            'name' => ucfirst($validated['category'])
        ]);
        $validated['category_id'] = $cat->id;
    }

    $category = \App\Models\Category::find($validated['category_id']);
    $validated['category'] = strtolower($category->name);

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
        'category_id' => ['nullable', 'exists:categories,id'],
        'category'    => ['nullable', 'string', 'max:255'],
        'brand'       => ['nullable', 'string', 'max:255'],
        'sku'         => ['nullable', 'string', 'max:80', 'unique:products,sku,' . $product->id],
        'price'       => ['required', 'integer', 'min:1'],
        'stock'       => ['required', 'integer', 'min:0'],
        'unit'        => ['required', 'string', 'max:40'],
        'description' => ['nullable', 'string'],
        'is_active'   => ['nullable', 'boolean'],
    ]);

    if (empty($validated['category_id'])) {
        if (empty($validated['category'])) {
            return back()->withErrors(['category_id' => 'Kategori wajib dipilih.'])->withInput();
        }
        $cat = \App\Models\Category::firstOrCreate([
            'name' => ucfirst($validated['category'])
        ]);
        $validated['category_id'] = $cat->id;
    }

    $category = \App\Models\Category::find($validated['category_id']);
    $validated['category'] = strtolower($category->name);

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

// ── Category CRUD ─────────────────────────────────────────────────────────────

Route::post('/categories', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
        'description' => ['nullable', 'string'],
    ]);

    \App\Models\Category::create($validated);

    return redirect()->route('admin.products')->with('status', 'Kategori berhasil ditambahkan.');
})->name('categories.store');

Route::put('/categories/{category}', function (Request $request, \App\Models\Category $category) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
        'description' => ['nullable', 'string'],
    ]);

    $category->update($validated);

    // Also update products string representation for backward compatibility if name changed
    $products = $category->products;
    foreach ($products as $product) {
        $product->update([
            'category' => strtolower($category->name)
        ]);
    }

    return redirect()->route('admin.products')->with('status', 'Kategori berhasil diperbarui.');
})->name('categories.update');

Route::delete('/categories/{category}', function (\App\Models\Category $category) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $category->delete();

    return redirect()->route('admin.products')->with('status', 'Kategori berhasil dihapus.');
})->name('categories.destroy');
