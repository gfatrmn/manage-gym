<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            if (! Schema::hasColumn('gym_members', 'package_status')) {
                $table->string('package_status', 20)->nullable()->after('membership_plan');
            }

            if (! Schema::hasColumn('gym_members', 'guest_visit_type')) {
                $table->string('guest_visit_type', 20)->nullable()->after('package_status');
            }

            if (! Schema::hasColumn('gym_members', 'can_check_in')) {
                $table->boolean('can_check_in')->default(false)->after('guest_visit_type');
            }
        });
    }

    public function down(): void
    {
        foreach (['can_check_in', 'guest_visit_type', 'package_status'] as $column) {
            if (Schema::hasColumn('gym_members', $column)) {
                Schema::table('gym_members', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
