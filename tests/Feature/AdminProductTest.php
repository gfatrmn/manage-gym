<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_products(): void
    {
        $session = [
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ];

        $this->withSession($session)
            ->post(route('admin.products.store'), [
                'name' => 'Whey Protein Gold',
                'category' => 'suplemen',
                'brand' => 'ON',
                'sku' => 'WHEY-001',
                'price' => 350000,
                'stock' => 12,
                'unit' => 'pcs',
                'description' => 'Protein untuk recovery',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.products'));

        $product = Product::query()->firstOrFail();

        $this->assertDatabaseHas('products', [
            'name' => 'Whey Protein Gold',
            'category' => 'suplemen',
            'price' => 350000,
            'stock' => 12,
            'is_active' => 1,
        ]);

        $this->withSession($session)
            ->put(route('admin.products.update', $product), [
                'name' => 'Vitamin C 1000',
                'category' => 'vitamin',
                'brand' => 'Healthy Co',
                'sku' => 'VITC-1000',
                'price' => 95000,
                'stock' => 20,
                'unit' => 'botol',
                'description' => 'Vitamin harian',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Vitamin C 1000',
            'category' => 'vitamin',
            'price' => 95000,
            'stock' => 20,
            'is_active' => 0,
        ]);

        $this->withSession($session)
            ->get(route('admin.products'))
            ->assertOk()
            ->assertSee('Master Produk Suplemen')
            ->assertSee('Daftar Produk')
            ->assertSee('Vitamin C 1000')
            ->assertSee('Rp95.000');

        $this->withSession($session)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_crud_categories(): void
    {
        $session = [
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ];

        // Store category
        $this->withSession($session)
            ->post(route('admin.categories.store'), [
                'name' => 'Aksesoris',
                'description' => 'Barang kelengkapan gym',
            ])
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Aksesoris',
            'description' => 'Barang kelengkapan gym',
        ]);

        $category = \App\Models\Category::where('name', 'Aksesoris')->firstOrFail();

        // Update category
        $this->withSession($session)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Aksesoris Gym',
                'description' => 'Barang aksesoris latihan',
            ])
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Aksesoris Gym',
            'description' => 'Barang aksesoris latihan',
        ]);

        // Destroy category
        $this->withSession($session)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.products'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
