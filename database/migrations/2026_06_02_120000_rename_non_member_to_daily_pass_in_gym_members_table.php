<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gym_members')) {
            return;
        }

        if (Schema::hasColumn('gym_members', 'status')) {
            DB::statement("ALTER TABLE gym_members MODIFY status ENUM('member', 'non_member', 'daily_pass') NOT NULL DEFAULT 'member'");
            DB::table('gym_members')->where('status', 'non_member')->update(['status' => 'daily_pass']);
            DB::statement("ALTER TABLE gym_members MODIFY status ENUM('member', 'daily_pass') NOT NULL DEFAULT 'member'");
        }

        if (Schema::hasColumn('gym_members', 'member_status')) {
            DB::statement("ALTER TABLE gym_members MODIFY member_status ENUM('member', 'non_member', 'daily_pass') NOT NULL DEFAULT 'member'");
            DB::table('gym_members')->where('member_status', 'non_member')->update(['member_status' => 'daily_pass']);
            DB::statement("ALTER TABLE gym_members MODIFY member_status ENUM('member', 'daily_pass') NOT NULL DEFAULT 'member'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('gym_members')) {
            return;
        }

        if (Schema::hasColumn('gym_members', 'status')) {
            DB::statement("ALTER TABLE gym_members MODIFY status ENUM('member', 'daily_pass', 'non_member') NOT NULL DEFAULT 'member'");
            DB::table('gym_members')->where('status', 'daily_pass')->update(['status' => 'non_member']);
            DB::statement("ALTER TABLE gym_members MODIFY status ENUM('member', 'non_member') NOT NULL DEFAULT 'member'");
        }

        if (Schema::hasColumn('gym_members', 'member_status')) {
            DB::statement("ALTER TABLE gym_members MODIFY member_status ENUM('member', 'daily_pass', 'non_member') NOT NULL DEFAULT 'member'");
            DB::table('gym_members')->where('member_status', 'daily_pass')->update(['member_status' => 'non_member']);
            DB::statement("ALTER TABLE gym_members MODIFY member_status ENUM('member', 'non_member') NOT NULL DEFAULT 'member'");
        }
    }
};
