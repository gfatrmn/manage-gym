<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();
        $memberSection = $request->string('section', 'active')->lower()->value() === 'expired' ? 'expired' : 'active';
        $memberSearch = trim($request->string('q')->value());

        $hasCheckinHistory = Schema::hasTable('gym_checkins') && Schema::hasColumn('gym_checkins', 'gym_member_id');
        $hasProductHistory = Schema::hasTable('cashier_transactions') && Schema::hasColumn('cashier_transactions', 'product_id');
        $hasMemberHistory = Schema::hasTable('member_histories') && Schema::hasColumn('member_histories', 'gym_member_id');

        $baseQuery = GymMember::query()->where('status', 'member');

        if ($hasMemberHistory) {
            $baseQuery->withCount([
                'checkinHistories as checkins_count',
                'productPurchaseHistories as product_transactions_count',
            ]);
        } elseif ($hasCheckinHistory) {
            $baseQuery->withCount(['verifiedCheckins as checkins_count']);
        }

        if (! $hasMemberHistory && $hasProductHistory) {
            $baseQuery->withCount(['productTransactions as product_transactions_count']);
        }

        if ($memberSearch !== '') {
            $baseQuery->where(function ($q) use ($memberSearch) {
                $q->where('full_name', 'like', '%' . $memberSearch . '%')
                    ->orWhere('email', 'like', '%' . $memberSearch . '%')
                    ->orWhere('phone', 'like', '%' . $memberSearch . '%')
                    ->orWhere('checkin_code', 'like', '%' . $memberSearch . '%');
            });
        }

        $baseQuery->latest();

        $totalActiveCount = GymMember::query()->where('status', 'member')->whereDate('expires_at', '>=', $today)->count();
        $totalExpiredCount = GymMember::query()->where('status', 'member')->where(function ($q) use ($today) {
            $q->whereNull('expires_at')->orWhereDate('expires_at', '<', $today);
        })->count();
        $totalMembersCount = GymMember::query()->where('status', 'member')->count();
        $expiringSoonCount = GymMember::query()->where('status', 'member')->whereBetween('expires_at', [$today, $today->copy()->addDays(7)])->count();

        $membersQuery = $memberSection === 'expired'
            ? (clone $baseQuery)->where(function ($q) use ($today) {
                $q->whereNull('expires_at')->orWhereDate('expires_at', '<', $today);
            })
            : (clone $baseQuery)->whereDate('expires_at', '>=', $today);

        $membersPaginator = $membersQuery->paginate(10)->withQueryString();

        $historyRelations = [];
        if ($hasMemberHistory) {
            $historyRelations = ['checkinHistories', 'verifiedCheckins', 'productPurchaseHistories.product'];
        } elseif ($hasCheckinHistory) {
            $historyRelations = ['verifiedCheckins'];
        }
        if (! $hasMemberHistory && $hasProductHistory) {
            $historyRelations[] = 'productTransactions.product';
        }
        if ($historyRelations !== []) {
            $membersPaginator->getCollection()->load($historyRelations);
        }

        $members = $membersPaginator->through(function (GymMember $member) {
            $lifecycle = RouteHelpers::memberLifecycleStatus($member);

            $checkinLogs = $member->verifiedCheckins->map(fn ($item) => [
                'id' => $item->id,
                'checked_in_at' => $item->checked_in_at?->translatedFormat('d M Y, H:i'),
                'method' => ucfirst(str_replace('_', ' ', (string) ($item->checkin_method ?? 'admin'))),
                'notes' => $item->notes,
            ])->take(12)->values();

            $productLogs = ($member->relationLoaded('productPurchaseHistories') && $member->productPurchaseHistories->isNotEmpty()
                ? $member->productPurchaseHistories->map(fn ($item) => [
                    'id' => $item->id,
                    'title' => $item->title ?? $item->product?->name ?? 'Produk',
                    'quantity' => $item->quantity ?? 1,
                    'amount' => RouteHelpers::formatCurrency($item->amount ?? 0),
                    'occurred_at' => $item->occurred_at?->translatedFormat('d M Y, H:i'),
                ])
                : ($member->relationLoaded('productTransactions')
                    ? $member->productTransactions->map(fn ($item) => [
                        'id' => $item->id,
                        'title' => $item->product?->name ?? $item->transaction_type ?? 'Produk',
                        'quantity' => $item->quantity ?? 1,
                        'amount' => RouteHelpers::formatCurrency($item->amount ?? 0),
                        'occurred_at' => $item->transaction_at?->translatedFormat('d M Y, H:i'),
                    ])
                    : collect()))->take(12)->values();

            return [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'email' => $member->email,
                'phone' => $member->phone,
                'checkin_code' => $member->checkin_code,
                'membership_plan' => $member->membership_plan ?? 'Membership 1 Bulan',
                'payment_method' => $member->payment_method,
                'payment_method_label' => $member->payment_method ? strtoupper($member->payment_method) : '-',
                'joined_at' => $member->joined_at?->format('Y-m-d'),
                'joined_at_label' => $member->joined_at?->translatedFormat('d M Y') ?? '-',
                'expires_at' => $member->expires_at?->format('Y-m-d'),
                'expires_at_label' => $member->expires_at?->translatedFormat('d M Y') ?? '-',
                'notes' => $member->notes,
                'profile_photo_url' => $member->profile_photo_url,
                'profile_initials' => $member->profile_initials,
                'lifecycle' => $lifecycle,
                'checkins_count' => $member->checkins_count ?? $member->verifiedCheckins->count(),
                'product_transactions_count' => $member->product_transactions_count ?? 0,
                'checkin_logs' => $checkinLogs,
                'product_logs' => $productLogs,
            ];
        });

        return Inertia::render('Admin/Members', [
            'pageTitle' => 'Member - Arena Gym',
            'activePage' => 'members',
            'memberSection' => $memberSection,
            'memberSearch' => $memberSearch,
            'members' => $members,
            'totalActiveCount' => $totalActiveCount,
            'totalExpiredCount' => $totalExpiredCount,
            'totalMembersCount' => $totalMembersCount,
            'expiringSoonCount' => $expiringSoonCount,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:gym_members,email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'joined_at' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $joinedAt = Carbon::parse($validated['joined_at'])->startOfDay();
        $expiresAt = $joinedAt->copy()->addMonthNoOverflow();
        $photoPath = $request->hasFile('profile_photo')
            ? $request->file('profile_photo')->store('member-photos', 'public')
            : null;

        DB::transaction(function () use ($validated, $joinedAt, $expiresAt, $photoPath) {
            $user = User::query()->create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'login' => $validated['email'],
                'role' => 'member',
                'password' => bcrypt(Str::random(12)),
            ]);

            GymMember::query()->create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'member_status' => 'member',
                'membership_plan' => 'Membership 1 Bulan',
                'package_status' => 'active',
                'payment_amount' => 90000,
                'can_check_in' => true,
                'joined_at' => $joinedAt,
                'expires_at' => $expiresAt,
                'payment_method' => $validated['payment_method'],
                'status' => 'member',
                'checkin_code' => RouteHelpers::generateMemberCheckinCode(),
                'profile_photo_path' => $photoPath,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.members')
            ->with('status', 'Member berhasil ditambahkan!');
    }

    public function update(Request $request, GymMember $member): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('gym_members', 'email')->ignore($member->id),
                Rule::unique('users', 'email')->ignore($member->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'joined_at' => ['nullable', 'date'],
            'payment_method' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $validated, $member) {
            $data = [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? $member->email,
                'phone' => $validated['phone'] ?? $member->phone,
                'payment_method' => $validated['payment_method'],
                'joined_at' => $validated['joined_at'] ?? $member->joined_at,
                'notes' => array_key_exists('notes', $validated) ? $validated['notes'] : $member->notes,
            ];

            if (! empty($validated['duration'])) {
                $months = (int) $validated['duration'];
                $today = Carbon::today();
                $baseDate = ($member->expires_at && $member->expires_at->copy()->startOfDay()->gte($today))
                    ? $member->expires_at->copy()->startOfDay()
                    : $today;

                $data['expires_at'] = $baseDate->addMonthsNoOverflow($months);
                $data['status'] = 'member';
                $data['member_status'] = 'member';
                $data['package_status'] = 'active';
                $data['membership_plan'] = "Membership {$months} Bulan";

                $hargaPerBulan = 90000;
                $amount = $months * $hargaPerBulan;

                CashierTransaction::query()->create([
                    'invoice' => RouteHelpers::generateInvoice('MP'),
                    'gym_member_id' => $member->id,
                    'customer_name' => $data['full_name'],
                    'transaction_group' => 'member_payment',
                    'transaction_type' => "Perpanjangan Membership {$months} Bulan",
                    'amount' => $amount,
                    'paid_amount' => $amount,
                    'change_amount' => 0,
                    'quantity' => $months,
                    'payment_method' => strtolower($data['payment_method']),
                    'payment_status' => 'verified',
                    'receipt_status' => 'ready',
                    'transaction_at' => now(),
                    'notes' => "Perpanjangan member oleh Admin: {$months} Bulan",
                ]);
            }

            if ($request->hasFile('profile_photo')) {
                if ($member->profile_photo_path) {
                    Storage::disk('public')->delete($member->profile_photo_path);
                }
                $data['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
            }

            $member->update($data);

            if ($member->user) {
                $member->user->update([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                ]);
            }
        });

        return redirect()->route('admin.members')->with('status', 'Data member dan masa aktif berhasil diperbarui.');
    }

    public function destroy(GymMember $member): RedirectResponse
    {
        DB::transaction(function () use ($member) {
            if ($member->profile_photo_path) {
                Storage::disk('public')->delete($member->profile_photo_path);
            }

            $user = $member->user;
            $member->delete();

            if ($user && $user->role === 'member') {
                $user->delete();
            }
        });

        return redirect()->route('admin.members')->with('status', 'Member berhasil dihapus.');
    }
}

