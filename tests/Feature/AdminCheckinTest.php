<?php

namespace Tests\Feature;

use App\Models\GymMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_member_checkin_with_date_and_time(): void
    {
        $member = GymMember::query()->create([
            'full_name' => 'Rina Pratama',
            'email' => 'rina@example.com',
            'phone' => '08123456789',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'can_check_in' => false,
            'joined_at' => now()->subDays(7),
            'expires_at' => now()->addDays(23),
        ]);

        $response = $this
            ->withSession([
                'auth' => [
                    'role' => 'admin',
                    'login' => 'admin',
                ],
            ])
            ->post(route('admin.checkins.store'), [
                'gym_member_id' => $member->id,
                'checkin_date' => now()->toDateString(),
                'checkin_time' => '07:30',
                'notes' => 'Latihan pagi',
            ]);

        $response->assertRedirect(route('admin.checkins'));

        $this->assertDatabaseHas('gym_checkins', [
            'gym_member_id' => $member->id,
            'checked_in_at' => now()->toDateString().' 07:30:00',
            'notes' => 'Latihan pagi',
        ]);

        $this->get(route('admin.checkins'))
            ->assertOk()
            ->assertSee('Rina Pratama')
            ->assertSee(now()->format('d M Y'))
            ->assertSee('07:30');
    }

    public function test_admin_can_store_member_checkin_by_barcode(): void
    {
        $member = GymMember::query()->create([
            'full_name' => 'Dewa Barcode',
            'email' => 'dewa@example.com',
            'phone' => '08123456780',
            'checkin_code' => 'AGM-BARCODE01',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'can_check_in' => false,
            'joined_at' => now()->subDays(7),
            'expires_at' => now()->addDays(23),
        ]);

        $response = $this
            ->withSession([
                'auth' => [
                    'role' => 'admin',
                    'login' => 'admin',
                ],
            ])
            ->post(route('admin.checkins.store'), [
                'checkin_code' => 'agm-barcode01',
                'notes' => 'Scan barcode di front desk',
            ]);

        $response->assertRedirect(route('admin.checkins'));

        $this->assertDatabaseHas('gym_checkins', [
            'gym_member_id' => $member->id,
            'notes' => 'Scan barcode di front desk',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.checkins'))
            ->assertOk()
            ->assertSee('Scan Barcode Member')
            ->assertSee('Buka Kamera Scan')
            ->assertSee('Dewa Barcode');
    }
}
