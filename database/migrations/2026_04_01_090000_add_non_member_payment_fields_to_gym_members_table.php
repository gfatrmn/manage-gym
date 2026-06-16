<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            if (! Schema::hasColumn('gym_members', 'payment_method')) {
                $table->string('payment_method', 30)->nullable()->after('guest_visit_type');
            }

            if (! Schema::hasColumn('gym_members', 'payment_amount')) {
                $table->unsignedInteger('payment_amount')->default(30000)->after('payment_method');
            }

            if (! Schema::hasColumn('gym_members', 'visit_date')) {
                $table->date('visit_date')->nullable()->after('payment_amount');
            }
        });

        DB::table('gym_members')
            ->where('member_status', 'daily_pass')
            ->update([
                'payment_method' => 'cash',
                'payment_amount' => 30000,
                'visit_date' => DB::raw('COALESCE(DATE(created_at), CURRENT_DATE)'),
            ]);
    }

    public function down(): void
    {
        foreach (['visit_date', 'payment_amount', 'payment_method'] as $column) {
            if (Schema::hasColumn('gym_members', $column)) {
                Schema::table('gym_members', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
