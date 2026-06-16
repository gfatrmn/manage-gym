<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_members', function (Blueprint $table) {
            if (! Schema::hasColumn('gym_members', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('checkin_code');
            }

            if (! Schema::hasColumn('gym_members', 'profile_photo_change_count')) {
                $table->unsignedTinyInteger('profile_photo_change_count')->default(0)->after('profile_photo_path');
            }
        });

        if (! Schema::hasTable('profile_photo_change_requests')) {
            Schema::create('profile_photo_change_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedBigInteger('gym_member_id')->nullable();
                $table->string('requested_photo_path');
                $table->string('status', 30)->default('pending');
                $table->timestamp('reviewed_at')->nullable();
                $table->string('reviewed_by')->nullable();
                $table->timestamps();

                $table->index(['gym_member_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_photo_change_requests');

        Schema::table('gym_members', function (Blueprint $table) {
            if (Schema::hasColumn('gym_members', 'profile_photo_change_count')) {
                $table->dropColumn('profile_photo_change_count');
            }
        });
    }
};
