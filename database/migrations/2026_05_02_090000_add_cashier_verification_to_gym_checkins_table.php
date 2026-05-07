<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_checkins', function (Blueprint $table) {
            $table->string('verification_status', 20)->default('verified')->after('checkin_method');
            $table->string('submitted_name')->nullable()->after('verification_status');
            $table->string('submitted_phone', 30)->nullable()->after('submitted_name');
            $table->timestamp('verified_at')->nullable()->after('submitted_phone');
            $table->string('verified_by')->nullable()->after('verified_at');
        });

        DB::table('gym_checkins')
            ->whereNull('verification_status')
            ->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('gym_checkins', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'submitted_name',
                'submitted_phone',
                'verified_at',
                'verified_by',
            ]);
        });
    }
};
