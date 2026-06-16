<?php

namespace Tests\Feature;

use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_member_with_automatic_monthly_package(): void
    {
        $response = $this
            ->withSession([
                'auth' => [
                    'role' => 'admin',
                    'login' => 'admin',
                ],
            ])
            ->post(route('admin.members.store'), [
                'full_name' => 'Member Bulanan',
                'email' => 'member@example.com',
                'phone' => '08123456789',
                'payment_method' => 'qris',
                'joined_at' => now()->toDateString(),
                'notes' => 'Daftar baru',
            ]);

        $response->assertRedirect(route('admin.members'));

        $this->assertDatabaseHas('gym_members', [
            'full_name' => 'Member Bulanan',
            'member_status' => 'member',
            'membership_plan' => 'Membership 1 Bulan',
            'payment_method' => 'qris',
            'payment_amount' => 90000,
            'package_status' => 'active',
            'joined_at' => now()->startOfDay()->toDateTimeString(),
            'expires_at' => now()->copy()->startOfDay()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $this->get(route('admin.members'))
            ->assertOk()
            ->assertSee('QRIS')
            ->assertSee('Member Bulanan');
    }

    public function test_member_page_shows_detail_action_and_monthly_training_history(): void
    {
        $member = GymMember::query()->create([
            'full_name' => 'Raka Pradana',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'joined_at' => now()->subDays(10),
            'expires_at' => now()->addDays(20),
        ]);

        GymCheckin::query()->create([
            'gym_member_id' => $member->id,
            'checked_in_at' => Carbon::parse('2026-04-10 07:30:00'),
            'checkin_method' => 'admin',
            'notes' => 'Latihan pagi',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.members'))
            ->assertOk()
            ->assertSee('Detail')
            ->assertSee('QR Code Member')
            ->assertSee('Riwayat Check-in')
            ->assertSee('Raka Pradana')
            ->assertSee('10 Apr 2026');
    }

    public function test_admin_can_search_member_data(): void
    {
        GymMember::query()->create([
            'full_name' => 'Rina Saputri',
            'email' => 'rina@example.com',
            'phone' => '081111111111',
            'checkin_code' => 'AG-RINA-01',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'joined_at' => now()->subDays(7),
            'expires_at' => now()->addDays(23),
        ]);

        GymMember::query()->create([
            'full_name' => 'Budi Hartono',
            'email' => 'budi@example.com',
            'phone' => '082222222222',
            'checkin_code' => 'AG-BUDI-01',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'qris',
            'payment_amount' => 50000,
            'joined_at' => now()->subDays(5),
            'expires_at' => now()->addDays(25),
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.members', ['q' => 'rina']))
            ->assertOk()
            ->assertSee('Cari nama atau no. HP')
            ->assertSee('Rina Saputri')
            ->assertDontSee('Budi Hartono');
    }
}
