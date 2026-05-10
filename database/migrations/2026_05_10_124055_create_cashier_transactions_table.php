<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cashier_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique();
            $table->string('customer_name');
            $table->foreignId('gym_member_id')->nullable()->constrained('gym_members')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->string('payment_status')->default('verified');
            $table->string('type'); // membership_renewal, new_membership, dll
            $table->text('description')->nullable();
            $table->timestamp('transaction_at');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cashier_transactions');
    }
};
