<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique();
            $table->foreignId('gym_member_id')->nullable()->constrained('gym_members')->nullOnDelete();
            $table->string('customer_name');
            $table->string('transaction_group', 30);
            $table->string('transaction_type');
            $table->unsignedInteger('amount');
            $table->string('payment_method', 30);
            $table->string('payment_status', 20)->default('pending');
            $table->string('receipt_status', 20)->default('pending');
            $table->dateTime('transaction_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_transactions');
    }
};
