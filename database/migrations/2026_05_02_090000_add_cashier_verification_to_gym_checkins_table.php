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
            if (! Schema::hasColumn('gym_checkins', 'verification_status')) {
                $table->string('verification_status', 20)->default('verified')->after('checkin_method');
            }

            if (! Schema::hasColumn('gym_checkins', 'submitted_name')) {
                $table->string('submitted_name')->nullable()->after('verification_status');
            }

            if (! Schema::hasColumn('gym_checkins', 'submitted_phone')) {
                $table->string('submitted_phone', 30)->nullable()->after('submitted_name');
            }

            if (! Schema::hasColumn('gym_checkins', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('submitted_phone');
            }

            if (! Schema::hasColumn('gym_checkins', 'verified_by')) {
                $table->string('verified_by')->nullable()->after('verified_at');
            }

            if (! Schema::hasColumn('gym_checkins', 'notes')) {
                $table->text('notes')->nullable()->after('verified_by');
            }
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
        foreach (['notes', 'verified_by', 'verified_at', 'submitted_phone', 'submitted_name', 'verification_status'] as $column) {
            if (Schema::hasColumn('gym_checkins', $column)) {
                Schema::table('gym_checkins', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
