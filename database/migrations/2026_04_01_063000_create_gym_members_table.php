<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gym_members')) {
            Schema::table('gym_members', function (Blueprint $table) {
                if (! Schema::hasColumn('gym_members', 'member_status')) {
                    $table->enum('member_status', ['member', 'daily_pass'])->default('member')->after('phone');
                }

                if (! Schema::hasColumn('gym_members', 'membership_plan')) {
                    $table->string('membership_plan')->nullable()->after('member_status');
                }

                if (! Schema::hasColumn('gym_members', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });

            return;
        }

        Schema::create('gym_members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->enum('member_status', ['member', 'daily_pass'])->default('member');
            $table->string('membership_plan')->nullable();
            $table->date('joined_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('gym_members')) {
            return;
        }

        foreach (['notes', 'membership_plan', 'member_status'] as $column) {
            if (Schema::hasColumn('gym_members', $column)) {
                Schema::table('gym_members', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
