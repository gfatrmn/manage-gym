<?php

namespace App\Http\Controllers\Cashier;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $members = GymMember::query()
            ->where('status', 'member')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('checkin_code', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', function ($q) {
                $q->whereDate('expires_at', '>=', now()->toDateString());
            })
            ->when($status === 'expired', function ($q) {
                $q->whereDate('expires_at', '<', now()->toDateString());
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (GymMember $member) => [
                'id'                   => $member->id,
                'full_name'            => $member->full_name,
                'email'                => $member->email,
                'phone'                => $member->phone ?: '-',
                'checkin_code'         => $member->checkin_code,
                'membership_plan'      => $member->membership_plan ?? 'Bulanan',
                'joined_at_label'      => $member->joined_at?->translatedFormat('d M Y') ?? '-',
                'expires_at_label'     => $member->expires_at?->translatedFormat('d M Y') ?? '-',
                'profile_photo_url'    => $member->profile_photo_url,
                'profile_initials'     => $member->profile_initials,
                'lifecycle'            => RouteHelpers::memberLifecycleStatus($member),
                'remaining_days'       => $member->expires_at && $member->expires_at->isFuture()
                    ? max(0, (int) now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false))
                    : 0,
            ]);

        return Inertia::render('Cashier/Members', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Data Member - Kasir Arena Gym',
            'activePage' => 'cashier.members',
            'members'    => $members,
            'filters'    => [
                'q'      => $search,
                'status' => $status,
            ],
        ]));
    }
}
