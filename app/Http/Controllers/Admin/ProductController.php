<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->with('categoryRelation:id,name')
            ->orderByDesc('is_active')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category' => $product->category,
                'category_name' => $product->categoryRelation?->name ?? ucfirst((string) $product->category),
                'brand' => $product->brand,
                'sku' => $product->sku,
                'price' => $product->price,
                'price_formatted' => RouteHelpers::formatCurrency($product->price),
                'stock' => $product->stock,
                'unit' => $product->unit,
                'description' => $product->description,
                'is_active' => $product->is_active,
            ]);

        $categories = Category::query()->withCount('products')->orderBy('name')->get();

        return Inertia::render('Admin/Products', [
            'pageTitle' => 'Master Produk Suplemen - Arena Gym',
            'activePage' => 'products',
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        Product::query()->create($validated);

        return redirect()->route('admin.products')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        $product->update($validated);

        return redirect()->route('admin.products')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products')->with('status', 'Produk berhasil dihapus.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::query()->create($validated);

        return redirect()->route('admin.products')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);
        Product::query()->where('category_id', $category->id)->update([
            'category' => strtolower($category->name),
        ]);

        return redirect()->route('admin.products')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.products')->with('status', 'Kategori berhasil dihapus.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku' . ($product ? ',' . $product->id : '')],
            'price' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['category_id'])) {
            $request->validate(['category' => ['required', 'string', 'max:255']]);
            $category = Category::query()->firstOrCreate([
                'name' => ucfirst(trim((string) $validated['category'])),
            ]);
            $validated['category_id'] = $category->id;
        } else {
            $category = Category::query()->findOrFail($validated['category_id']);
        }

        $validated['category'] = strtolower($category->name);
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : ($product ? $product->is_active : true);

        return $validated;
    }
}

