<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->string('profile_photo_pending_path')->nullable()->after('profile_photo_path');
            $table->enum('profile_photo_pending_status', ['pending', 'approved', 'rejected'])->nullable()->after('profile_photo_pending_path');
        });
    }

    public function down(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_pending_path', 'profile_photo_pending_status']);
        });
    }
};
