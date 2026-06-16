<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_renewal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('gym_member_id')->nullable();
            $table->string('plan')->default('membership_1_bulan');
            $table->unsignedTinyInteger('duration_months')->default(1);
            $table->unsignedInteger('amount')->default(90000);
            $table->string('payment_method', 30);
            $table->string('payment_proof_path')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamps();

            $table->index(['gym_member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_renewal_requests');
    }
};
