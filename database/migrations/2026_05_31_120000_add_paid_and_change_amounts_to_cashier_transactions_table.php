<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('cashier_transactions', 'paid_amount')) {
                $table->unsignedInteger('paid_amount')->nullable()->after('amount');
            }

            if (! Schema::hasColumn('cashier_transactions', 'change_amount')) {
                $table->unsignedInteger('change_amount')->nullable()->after('paid_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('cashier_transactions', 'change_amount')) {
                $table->dropColumn('change_amount');
            }

            if (Schema::hasColumn('cashier_transactions', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
