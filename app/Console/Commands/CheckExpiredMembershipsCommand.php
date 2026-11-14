<?php

namespace App\Console\Commands;

use App\Models\GymMember;
use App\Models\Membership;
use Illuminate\Console\Command;

class CheckExpiredMembershipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:check-expired-memberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan perbarui status membership yang telah melewati tanggal akhir aktif';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->toDateString();

        // 1. Update memberships table
        $expiredMemberships = Membership::query()
            ->where('status', 'active')
            ->whereDate('end_date', '<', $today)
            ->update([
                'status' => 'expired',
                'status_reason' => 'Masa aktif berakhir otomatis oleh sistem scheduler',
            ]);

        // 2. Update gym_members table package_status
        $expiredMembers = GymMember::query()
            ->where('status', 'member')
            ->where('package_status', 'active')
            ->whereDate('expires_at', '<', $today)
            ->update([
                'package_status' => 'expired',
            ]);

        $this->info("Pemeriksaan selesai. {$expiredMemberships} membership dan {$expiredMembers} member diperbarui menjadi expired.");

        return Command::SUCCESS;
    }
}
