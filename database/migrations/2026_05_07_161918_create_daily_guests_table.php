<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_guests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone', 30)->nullable();
            $table->integer('payment_amount')->default(20000);
            $table->string('payment_method', 30)->default('cash');
            $table->timestamp('visit_at'); // Perhatikan nama kolom ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_guests');
    }
};
