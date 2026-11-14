<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\GymMember;
use App\Models\Product;
use App\Models\ProfilePhotoChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RefactoredWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_enforces_exact_role_and_member_expiry(): void
    {
        User::query()->create([
            'name' => 'Kasir Satu',
            'login' => 'kasir_satu',
            'email' => 'kasir@arenagym.local',
            'role' => 'cashier',
            'password' => Hash::make('secret123'),
        ]);

        // Cashier cannot log in under the admin role
        $this->post(route('login.submit'), [
            'role' => 'admin',
            'login' => 'kasir_satu',
            'password' => 'secret123',
        ])->assertSessionHasErrors('login');

        // Expired member cannot log in on /login either
        $expiredUser = User::query()->create([
            'name' => 'Member Expired',
            'login' => 'expired_member',
            'email' => 'expired@arenagym.local',
            'role' => 'member',
            'password' => Hash::make('secret123'),
        ]);

        GymMember::query()->create([
            'user_id' => $expiredUser->id,
            'full_name' => 'Member Expired',
            'email' => 'expired@arenagym.local',
            'status' => 'member',
            'member_status' => 'member',
            'joined_at' => now()->subMonths(2),
            'expires_at' => now()->subDay(),
            'checkin_code' => 'AG-EXP001',
        ]);

        $this->post(route('login.submit'), [
            'role' => 'member',
            'login' => 'expired_member',
            'password' => 'secret123',
        ])->assertSessionHasErrors('login');
    }

    public function test_unified_login_without_role_parameter_routes_to_correct_dashboard(): void
    {
        // Admin
        User::query()->create([
            'name' => 'Admin Test',
            'login' => 'adm_unified',
            'email' => 'adm@arenagym.local',
            'role' => 'admin',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('login.submit'), [
            'login' => 'adm_unified',
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        // Cashier
        User::query()->create([
            'name' => 'Cashier Test',
            'login' => 'csh_unified',
            'email' => 'csh@arenagym.local',
            'role' => 'cashier',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('login.submit'), [
            'login' => 'csh_unified',
            'password' => 'secret123',
        ])->assertRedirect(route('cashier.dashboard'));

        // Active Member
        $mbrUser = User::query()->create([
            'name' => 'Member Test',
            'login' => 'mbr_unified',
            'email' => 'mbr@arenagym.local',
            'role' => 'member',
            'password' => Hash::make('secret123'),
        ]);

        GymMember::query()->create([
            'user_id' => $mbrUser->id,
            'full_name' => 'Member Test',
            'email' => 'mbr@arenagym.local',
            'status' => 'member',
            'member_status' => 'member',
            'joined_at' => now(),
            'expires_at' => now()->addMonth(),
            'checkin_code' => 'AG-UNIF01',
        ]);

        $this->post(route('login.submit'), [
            'login' => 'mbr_unified',
            'password' => 'secret123',
        ])->assertRedirect(route('member.dashboard'));
    }

    public function test_active_member_can_view_inertia_portal_pages_and_submit_feedback(): void
    {
        $memberUser = User::query()->create([
            'name' => 'Sinta Member',
            'login' => 'sinta',
            'email' => 'sinta@arenagym.local',
            'role' => 'member',
            'password' => Hash::make('secret123'),
        ]);

        $member = GymMember::query()->create([
            'user_id' => $memberUser->id,
            'full_name' => 'Sinta Member',
            'email' => 'sinta@arenagym.local',
            'phone' => '081111222333',
            'status' => 'member',
            'member_status' => 'member',
            'membership_plan' => 'Membership 1 Bulan',
            'joined_at' => now()->subDays(5),
            'expires_at' => now()->addDays(25),
            'checkin_code' => 'AG-SINTA1',
        ]);

        $this->post(route('member.login.submit'), [
            'email' => 'sinta@arenagym.local',
            'password' => 'secret123',
        ])->assertRedirect(route('member.dashboard'));

        $this->get(route('member.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Member/Dashboard')
                ->where('member.id', $member->id)
                ->where('membershipStatus', 'member')
            );

        $this->get(route('member.barcode'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Member/Barcode')
                ->where('checkinCode', 'AG-SINTA1')
            );

        $this->post(route('member.feedback.submit'), [
            'category' => 'Fasilitas',
            'message' => 'AC ruang latihan terasa kurang dingin di sore hari.',
        ])->assertRedirect();

        $this->assertDatabaseHas('member_feedbacks', [
            'gym_member_id' => $member->id,
            'subject' => 'Fasilitas',
        ]);
    }

    public function test_admin_member_renewal_records_member_payment_group_and_syncs_user(): void
    {
        $user = User::query()->create([
            'name' => 'Dodi Lama',
            'login' => 'dodi@arenagym.local',
            'email' => 'dodi@arenagym.local',
            'role' => 'member',
            'password' => Hash::make('secret123'),
        ]);

        $member = GymMember::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Dodi Lama',
            'email' => 'dodi@arenagym.local',
            'status' => 'member',
            'member_status' => 'member',
            'joined_at' => '2026-10-01',
            'expires_at' => '2026-11-01',
            'payment_method' => 'cash',
            'checkin_code' => 'AG-DODI1',
        ]);

        $this->withSession([
            'auth' => ['role' => 'admin', 'login' => 'admin'],
        ])->put(route('admin.members.update', $member->id), [
            'full_name' => 'Dodi Baru',
            'email' => 'dodi.baru@arenagym.local',
            'payment_method' => 'cash',
            'duration' => 3,
        ])->assertRedirect(route('admin.members'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Dodi Baru',
            'email' => 'dodi.baru@arenagym.local',
        ]);

        $this->assertDatabaseHas('cashier_transactions', [
            'gym_member_id' => $member->id,
            'transaction_group' => 'member_payment',
            'amount' => 270000,
            'payment_status' => 'verified',
        ]);
    }

    public function test_admin_can_approve_profile_photo_request_using_storage_disk(): void
    {
        Storage::fake('public');

        $member = GymMember::query()->create([
            'full_name' => 'Tari Photo',
            'email' => 'tari@arenagym.local',
            'status' => 'member',
            'member_status' => 'member',
            'joined_at' => now(),
            'expires_at' => now()->addMonth(),
            'checkin_code' => 'AG-TARI1',
        ]);

        $pendingFile = UploadedFile::fake()->image('pending.jpg');
        $pendingPath = $pendingFile->store('member-photo-requests', 'public');

        $photoRequest = ProfilePhotoChangeRequest::query()->create([
            'gym_member_id' => $member->id,
            'requested_photo_path' => $pendingPath,
            'status' => 'pending',
        ]);

        $this->withSession([
            'auth' => ['role' => 'admin', 'name' => 'Admin Utama'],
        ])->post(route('admin.profile-photo-requests.approve', $photoRequest->id))
            ->assertRedirect(route('admin.profile-photo-requests'));

        $photoRequest->refresh();
        $member->refresh();

        $this->assertSame('approved', $photoRequest->status);
        $this->assertNotNull($member->profile_photo_path);
        Storage::disk('public')->assertExists($member->profile_photo_path);
    }

    public function test_cashier_pos_product_checkout_rolls_back_atomically_when_stock_is_insufficient(): void
    {
        $category = Category::query()->firstOrCreate(['name' => 'Suplemen']);
        $okProduct = Product::query()->create([
            'name' => 'Isotonik 500ml',
            'category_id' => $category->id,
            'category' => 'suplemen',
            'price' => 15000,
            'stock' => 10,
            'unit' => 'botol',
            'is_active' => true,
        ]);
        $lowProduct = Product::query()->create([
            'name' => 'Protein Bar',
            'category_id' => $category->id,
            'category' => 'suplemen',
            'price' => 30000,
            'stock' => 1,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $this->withSession([
            'auth' => ['role' => 'cashier', 'login' => 'kasir'],
        ])->post(route('cashier.transactions.store'), [
            'transaction_group' => 'product_sale',
            'customer_name' => 'Pembeli Uji',
            'payment_method' => 'cash',
            'paid_amount' => 100000,
            'product_ids' => [$okProduct->id, $lowProduct->id],
            'quantities' => [
                $okProduct->id => 2,
                $lowProduct->id => 5,
            ],
        ])->assertRedirect(route('cashier.transactions.products'))
            ->assertSessionHasErrors('product_ids');

        // Neither product stock was decremented and no partial transaction saved
        $this->assertSame(10, $okProduct->fresh()->stock);
        $this->assertSame(1, $lowProduct->fresh()->stock);
        $this->assertDatabaseCount('cashier_transactions', 0);
    }
}
