<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_member_id')->constrained('gym_members')->onDelete('cascade');
            $table->datetime('checked_in_at');
            $table->string('checkin_method', 20)->default('admin');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_checkins');
    }
};
