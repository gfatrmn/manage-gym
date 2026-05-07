<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 60);
            $table->unsignedInteger('amount');
            $table->string('payment_method', 30)->nullable();
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_records');
    }
};
