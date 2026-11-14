<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Models\ActivityLog;
use App\Models\CashierTransaction;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use App\Models\GymMember;
use App\Models\GymSetting;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PrdComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $cashierUser;
    protected User $memberUser;
    protected GymMember $member;
    protected MembershipPackage $monthlyPackage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'master_admin',
            'login' => 'admin_test',
            'is_active' => true,
        ]);

        $this->cashierUser = User::factory()->create([
            'role' => 'cashier',
            'login' => 'cashier_test',
            'is_active' => true,
        ]);

        $this->memberUser = User::factory()->create([
            'role' => 'member',
            'login' => 'member_test',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->member = GymMember::query()->create([
            'user_id' => $this->memberUser->id,
            'full_name' => 'Budi Santoso',
            'email' => $this->memberUser->email,
            'phone' => '08123456789',
            'gender' => 'male',
            'birth_date' => '1995-05-15',
            'address' => 'Jl. Mawar No. 12',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 150000,
            'can_check_in' => true,
            'joined_at' => Carbon::today()->subDays(10),
            'expires_at' => Carbon::today()->addDays(20),
            'checkin_code' => 'AGM-BUDI001',
            'status' => 'member',
        ]);

        $this->monthlyPackage = MembershipPackage::query()->create([
            'name' => 'Paket Reguler 1 Bulan',
            'duration_days' => 30,
            'price' => 150000,
            'facilities' => 'Gym, Locker, Shower',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_crud_and_toggle_membership_packages(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->post(route('admin.packages.store'), [
                'name' => 'Paket Mahasiswa 3 Bulan',
                'duration_days' => 90,
                'price' => 380000,
                'facilities' => 'Full Gym, Free Locker',
                'is_active' => true,
            ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('membership_packages', [
            'name' => 'Paket Mahasiswa 3 Bulan',
            'duration_days' => 90,
            'price' => 380000,
            'is_active' => 1,
        ]);

        $created = MembershipPackage::where('name', 'Paket Mahasiswa 3 Bulan')->firstOrFail();

        // Toggle status
        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->patch(route('admin.packages.toggle-status', $created->id))
            ->assertSessionHas('status');

        $this->assertFalse((bool) $created->fresh()->is_active);

        // Audit log created
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create_package',
            'entity_id' => $created->id,
        ]);
    }

    public function test_admin_can_manage_membership_and_suspend_with_reason(): void
    {
        $membership = Membership::query()->create([
            'member_id' => $this->member->id,
            'package_id' => $this->monthlyPackage->id,
            'start_date' => Carbon::today()->subDays(10),
            'end_date' => Carbon::today()->addDays(20),
            'price' => 150000,
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->get(route('admin.memberships.index'))
            ->assertOk();

        // Suspend membership
        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->patch(route('admin.memberships.status', $membership->id), [
                'status' => 'suspended',
                'status_reason' => 'Permintaan cuti luar kota selama 2 minggu',
            ])
            ->assertSessionHas('status');

        $this->assertSame('suspended', $membership->fresh()->status);
        $this->assertSame('Permintaan cuti luar kota selama 2 minggu', $membership->fresh()->status_reason);
        $this->assertSame('suspended', $this->member->fresh()->package_status);
    }

    public function test_admin_can_manage_expense_categories_and_expenses(): void
    {
        $category = ExpenseCategory::query()->create([
            'name' => 'Listrik & Air PLN',
            'description' => 'Biaya utilitas bulanan gym',
        ]);

        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->post(route('admin.expenses.store'), [
                'title' => 'Tagihan Listrik PLN Mei 2026',
                'category_id' => $category->id,
                'amount' => 1250000,
                'expense_date' => Carbon::today()->toDateString(),
                'payment_method' => 'transfer',
                'notes' => 'Bukti transfer terlampir',
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('expense_records', [
            'category_id' => $category->id,
            'title' => 'Tagihan Listrik PLN Mei 2026',
            'amount' => 1250000,
        ]);
    }

    public function test_admin_can_view_transaction_detail_invoice(): void
    {
        $trx = CashierTransaction::query()->create([
            'invoice' => 'INV-20260520-001',
            'gym_member_id' => $this->member->id,
            'customer_name' => $this->member->full_name,
            'transaction_group' => 'member_payment',
            'transaction_type' => 'Membership Bulanan',
            'amount' => 150000,
            'paid_amount' => 150000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'verified',
            'receipt_status' => 'ready',
            'transaction_at' => now(),
        ]);

        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->get(route('admin.transactions.show', $trx->id))
            ->assertOk();
    }

    public function test_admin_can_update_gym_settings(): void
    {
        $this->actingAs($this->adminUser)
            ->withSession(['auth' => ['role' => 'master_admin', 'login' => 'admin_test', 'user_id' => $this->adminUser->id]])
            ->post(route('admin.settings.update'), [
                'gym_name' => 'Arena Fitness Supreme',
                'gym_phone' => '0811-9988-7766',
                'gym_address' => 'Jl. Sudirman No. 88, Jakarta Pusat',
                'gym_email' => 'support@arenasupreme.com',
                'invoice_prefix' => 'INV-ARN',
                'timezone' => 'Asia/Jakarta',
                'invoice_footer' => 'Terima kasih atas kunjungan Anda di Arena Fitness Supreme!',
            ])
            ->assertSessionHas('status');

        $this->assertSame('Arena Fitness Supreme', GymSetting::get('gym_name'));
        $this->assertSame('INV-ARN', GymSetting::get('invoice_prefix'));
    }

    public function test_business_rule_br011_renewal_date_calculations(): void
    {
        Carbon::setTestNow('2026-06-01 08:00:00');

        // Case A: Member is ACTIVE (expires 2026-06-15)
        $activeMember = GymMember::query()->create([
            'full_name' => 'Active Member',
            'status' => 'member',
            'joined_at' => '2026-05-16',
            'expires_at' => '2026-06-15',
            'checkin_code' => 'AGM-ACT01',
        ]);

        $datesActive = RouteHelpers::calculateMembershipDates($activeMember, 30);
        // Start date must be expires_at + 1 day: 2026-06-16
        // End date must be start_date + 30 days - 1: 2026-07-15
        $this->assertSame('2026-06-16', $datesActive['start_date']);
        $this->assertSame('2026-07-15', $datesActive['end_date']);

        // Case B: Member is EXPIRED (expired on 2026-05-20, today is 2026-06-01)
        $expiredMember = GymMember::query()->create([
            'full_name' => 'Expired Member',
            'status' => 'member',
            'joined_at' => '2026-04-20',
            'expires_at' => '2026-05-20',
            'checkin_code' => 'AGM-EXP01',
        ]);

        $datesExpired = RouteHelpers::calculateMembershipDates($expiredMember, 30);
        // Start date must be today: 2026-06-01
        // End date must be 2026-06-01 + 30 - 1 = 2026-06-30
        $this->assertSame('2026-06-01', $datesExpired['start_date']);
        $this->assertSame('2026-06-30', $datesExpired['end_date']);

        Carbon::setTestNow();
    }

    public function test_check_expired_memberships_command_br014(): void
    {
        Carbon::setTestNow('2026-06-10 00:00:00');

        $expiredMember = GymMember::query()->create([
            'full_name' => 'Harusnya Expired',
            'status' => 'member',
            'package_status' => 'active',
            'expires_at' => '2026-06-09',
            'checkin_code' => 'AGM-AUTOEXP01',
        ]);

        $membership = Membership::query()->create([
            'member_id' => $expiredMember->id,
            'package_id' => $this->monthlyPackage->id,
            'start_date' => '2026-05-10',
            'end_date' => '2026-06-09',
            'price' => 150000,
            'status' => 'active',
        ]);

        $this->artisan('gym:check-expired-memberships')
            ->assertExitCode(0);

        $membership->refresh();
        $expiredMember->refresh();

        $this->assertSame('expired', $membership->status);
        $this->assertSame('expired', $expiredMember->package_status);

        Carbon::setTestNow();
    }

    public function test_member_portal_pages_and_password_change(): void
    {
        $memberSession = [
            'auth' => [
                'id' => $this->memberUser->id,
                'role' => 'member',
                'login' => 'member_test',
                'name' => 'Budi Santoso',
                'email' => $this->memberUser->email,
            ],
        ];

        // Member packages catalog
        $this->actingAs($this->memberUser)
            ->withSession($memberSession)
            ->get(route('member.packages'))
            ->assertOk();

        // Member payments history
        $this->actingAs($this->memberUser)
            ->withSession($memberSession)
            ->get(route('member.payments'))
            ->assertOk();

        // Member profile update
        $this->actingAs($this->memberUser)
            ->withSession($memberSession)
            ->post(route('member.profile.update'), [
                'name' => 'Budi Santoso Updated',
                'email' => $this->memberUser->email,
                'phone' => '08999999999',
                'gender' => 'male',
                'birth_date' => '1995-05-15',
                'address' => 'Jl. Thamrin No. 5',
            ])
            ->assertSessionHas('status');

        $this->assertSame('Budi Santoso Updated', $this->member->fresh()->full_name);
        $this->assertSame('08999999999', $this->member->fresh()->phone);

        // Member change password
        $this->actingAs($this->memberUser)
            ->withSession($memberSession)
            ->post(route('member.password.change'), [
                'current_password' => 'password123',
                'password' => 'newSecretPass123',
                'password_confirmation' => 'newSecretPass123',
            ])
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('newSecretPass123', $this->memberUser->fresh()->password));
    }
}
