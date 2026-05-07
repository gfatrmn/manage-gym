<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNonMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_non_member_with_automatic_payment_defaults(): void
    {
        $response = $this
            ->withSession([
                'auth' => [
                    'role' => 'admin',
                    'login' => 'admin',
                ],
            ])
            ->post(route('admin.non-members.store'), [
                'full_name' => 'Tamu Harian',
                'email' => 'tamu@example.com',
                'phone' => '08123456789',
                'payment_method' => 'qris',
                'notes' => 'Walk in pagi',
            ]);

        $response->assertRedirect(route('admin.non-members'));

        $this->assertDatabaseHas('gym_members', [
            'full_name' => 'Tamu Harian',
            'member_status' => 'non_member',
            'payment_method' => 'qris',
            'payment_amount' => 30000,
            'can_check_in' => false,
        ]);

        $this->get(route('admin.non-members'))
            ->assertOk()
            ->assertSee('Tamu Harian')
            ->assertSee('QRIS')
            ->assertSee('Rp30.000');
    }

    public function test_admin_can_store_multiple_non_member_visits_with_same_email(): void
    {
        $session = [
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ];

        $this->withSession($session)->post(route('admin.non-members.store'), [
            'full_name' => 'Tamu Pertama',
            'email' => 'guest@example.com',
            'phone' => '0811111111',
            'payment_method' => 'cash',
        ])->assertRedirect(route('admin.non-members'));

        $this->withSession($session)->post(route('admin.non-members.store'), [
            'full_name' => 'Tamu Kedua',
            'email' => 'guest@example.com',
            'phone' => '0822222222',
            'payment_method' => 'qris',
        ])->assertRedirect(route('admin.non-members'));

        $this->assertDatabaseCount('gym_members', 2);
        $this->assertDatabaseHas('gym_members', [
            'full_name' => 'Tamu Kedua',
            'member_status' => 'non_member',
            'payment_method' => 'qris',
            'payment_amount' => 30000,
        ]);
    }
}
