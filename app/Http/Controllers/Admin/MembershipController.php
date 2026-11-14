<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\GymMember;
use App\Models\Membership;
use App\Models\MembershipPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MembershipController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $memberships = Membership::query()
            ->with(['member:id,full_name,email,phone,checkin_code,status,profile_photo_path', 'package:id,name,duration_days,price', 'transaction:id,invoice,payment_method,payment_status'])
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('member', function ($mq) use ($search) {
                    $mq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('checkin_code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => Membership::count(),
            'active'    => Membership::where('status', 'active')->count(),
            'expired'   => Membership::where('status', 'expired')->count(),
            'suspended' => Membership::where('status', 'suspended')->count(),
            'pending'   => Membership::where('status', 'pending')->count(),
        ];

        return Inertia::render('Admin/Memberships', [
            'memberships' => $memberships,
            'stats'       => $stats,
            'filters'     => [
                'q'      => $search,
                'status' => $status,
            ],
        ]);
    }

    public function updateStatus(Request $request, Membership $membership): RedirectResponse
    {
        $validated = $request->validate([
            'status'        => ['required', 'in:active,suspended,cancelled,expired'],
            'status_reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $oldStatus = $membership->status;
        $newStatus = $validated['status'];

        $membership->update([
            'status'        => $newStatus,
            'status_reason' => $validated['status_reason'],
        ]);

        // Sync with member active state
        if ($membership->member) {
            if ($newStatus === 'suspended' || $newStatus === 'cancelled') {
                $membership->member->update(['package_status' => $newStatus]);
            } elseif ($newStatus === 'active') {
                $membership->member->update([
                    'package_status' => 'active',
                    'status'         => 'member',
                    'expires_at'     => $membership->end_date,
                ]);
            }
        }

        ActivityLog::record(
            action: 'update_membership_status',
            entityType: Membership::class,
            entityId: $membership->id,
            description: "Admin mengubah status membership #{$membership->id} ({$membership->member?->full_name}) dari {$oldStatus} ke {$newStatus}. Alasan: {$validated['status_reason']}",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus, 'status_reason' => $validated['status_reason']]
        );

        return back()->with('status', "Status membership berhasil diubah menjadi {$newStatus}.");
    }
}
