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
            $table->string('payment_method', 30)->nullable()->after('guest_visit_type');
            $table->unsignedInteger('payment_amount')->default(30000)->after('payment_method');
            $table->date('visit_date')->nullable()->after('payment_amount');
        });

        DB::table('gym_members')
            ->where('member_status', 'non_member')
            ->update([
                'payment_method' => 'cash',
                'payment_amount' => 30000,
                'visit_date' => DB::raw('COALESCE(DATE(created_at), CURRENT_DATE)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_amount', 'visit_date']);
        });
    }
};
