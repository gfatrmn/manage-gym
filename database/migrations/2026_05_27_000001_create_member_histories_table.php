<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_member_id')->constrained('gym_members')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('history_type', 30);
            $table->dateTime('occurred_at');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->string('source_type', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index(['gym_member_id', 'occurred_at']);
            $table->index(['source_type', 'source_id']);
        });

        if (Schema::hasTable('gym_checkins')) {
            DB::table('gym_checkins')
                ->whereNotNull('gym_member_id')
                ->where('verification_status', 'verified')
                ->orderBy('id')
                ->get()
                ->each(function ($checkin) {
                    DB::table('member_histories')->insert([
                        'gym_member_id' => $checkin->gym_member_id,
                        'history_type'  => 'checkin',
                        'occurred_at'   => $checkin->checked_in_at,
                        'title'         => 'Check-in member',
                        'description'   => 'Riwayat check-in lama',
                        'source_type'   => 'App\\Models\\GymCheckin',
                        'source_id'     => $checkin->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                });
        }

        if (Schema::hasTable('cashier_transactions') && Schema::hasColumn('cashier_transactions', 'product_id')) {
            DB::table('cashier_transactions')
                ->whereNotNull('gym_member_id')
                ->whereNotNull('product_id')
                ->orderBy('id')
                ->get()
                ->each(function ($transaction) {
                    DB::table('member_histories')->insert([
                        'gym_member_id' => $transaction->gym_member_id,
                        'product_id'    => $transaction->product_id,
                        'history_type'  => 'product_purchase',
                        'occurred_at'   => $transaction->transaction_at,
                        'title'         => $transaction->transaction_type ?? 'Pembelian barang',
                        'description'   => 'Riwayat pembelian lama',
                        'quantity'      => $transaction->quantity ?? 1,
                        'amount'        => $transaction->amount ?? null,
                        'source_type'   => 'App\\Models\\CashierTransaction',
                        'source_id'     => $transaction->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('member_histories');
    }
};
