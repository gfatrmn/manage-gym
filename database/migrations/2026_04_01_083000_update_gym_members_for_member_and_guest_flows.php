<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->string('package_status', 20)->nullable()->after('membership_plan');
            $table->string('guest_visit_type', 20)->nullable()->after('package_status');
            $table->boolean('can_check_in')->default(false)->after('guest_visit_type');
        });
    }

    public function down(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->dropColumn(['package_status', 'guest_visit_type', 'can_check_in']);
        });
    }
};
