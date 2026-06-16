<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            if (! Schema::hasColumn('gym_members', 'profile_photo_pending_path')) {
                $table->string('profile_photo_pending_path')->nullable()->after('profile_photo_path');
            }

            if (! Schema::hasColumn('gym_members', 'profile_photo_pending_status')) {
                $table->enum('profile_photo_pending_status', ['pending', 'approved', 'rejected'])->nullable()->after('profile_photo_pending_path');
            }
        });
    }

    public function down(): void
    {
        foreach (['profile_photo_pending_status', 'profile_photo_pending_path'] as $column) {
            if (Schema::hasColumn('gym_members', $column)) {
                Schema::table('gym_members', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
