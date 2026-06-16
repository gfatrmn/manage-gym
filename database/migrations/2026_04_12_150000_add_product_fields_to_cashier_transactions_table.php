<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('cashier_transactions', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('gym_member_id')->constrained('products')->nullOnDelete();
            }

            if (! Schema::hasColumn('cashier_transactions', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('cashier_transactions', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            if (Schema::hasColumn('cashier_transactions', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
