<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->dateTime('last_membership_reminder_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->dropColumn('last_membership_reminder_at');
        });
    }
};
