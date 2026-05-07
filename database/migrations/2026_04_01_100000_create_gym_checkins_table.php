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
            $table->foreignId('gym_member_id')->constrained('gym_members')->cascadeOnDelete();
            $table->dateTime('checked_in_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        $members = DB::table('gym_members')
            ->where('member_status', 'member')
            ->orderBy('id')
            ->limit(3)
            ->get(['id']);

        foreach ($members as $index => $member) {
            DB::table('gym_checkins')->insert([
                'gym_member_id' => $member->id,
                'checked_in_at' => Carbon::now()->subHours(3 - $index),
                'notes' => 'Latihan rutin member',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_checkins');
    }
};
