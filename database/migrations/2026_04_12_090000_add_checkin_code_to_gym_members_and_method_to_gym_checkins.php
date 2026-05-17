<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->string('checkin_code', 40)->nullable()->unique()->after('phone');
        });

        Schema::table('gym_checkins', function (Blueprint $table) {
            $table->string('checkin_method', 20)->default('admin')->after('checked_in_at');
        });

        DB::table('gym_members')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function ($member): void {
                DB::table('gym_members')
                    ->where('id', $member->id)
                    ->update([
                        'checkin_code' => 'AGM-'.strtoupper(Str::random(10)),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('gym_checkins', function (Blueprint $table) {
            $table->dropColumn('checkin_method');
        });

        Schema::table('gym_members', function (Blueprint $table) {
            $table->dropUnique('gym_members_checkin_code_unique');
            $table->dropColumn('checkin_code');
        });
    }
};
