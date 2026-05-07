<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 30);
            $table->string('brand')->nullable();
            $table->string('sku', 80)->nullable()->unique();
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->string('unit', 40)->default('pcs');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
