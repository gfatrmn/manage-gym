<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('gym_member_id')->constrained('products')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('quantity');
        });
    }
};
