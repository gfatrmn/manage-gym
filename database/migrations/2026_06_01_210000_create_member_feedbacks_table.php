<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('member_feedbacks')) {
            return;
        }

        Schema::create('member_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gym_member_id')->nullable()->constrained('gym_members')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('subject', 120);
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_feedbacks');
    }
};

