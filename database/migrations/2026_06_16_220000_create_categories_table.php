<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('category');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Migrate existing product categories to categories table and update products
        $existingCategories = DB::table('products')->whereNotNull('category')->pluck('category')->unique();
        
        // Ensure default 'Suplemen' and 'Vitamin' exist
        $defaults = ['suplemen', 'vitamin'];
        $allToMigrate = collect($existingCategories)->merge($defaults)->unique();

        foreach ($allToMigrate as $name) {
            $formattedName = ucfirst($name);
            // Check if already exists in case of duplicate runs
            $existingId = DB::table('categories')->where('name', $formattedName)->value('id');
            if (!$existingId) {
                $existingId = DB::table('categories')->insertGetId([
                    'name' => $formattedName,
                    'description' => 'Kategori produk ' . $formattedName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('products')->where('category', $name)->update(['category_id' => $existingId]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        Schema::dropIfExists('categories');
    }
};
