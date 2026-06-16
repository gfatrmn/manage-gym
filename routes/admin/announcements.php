<?php

use App\Helpers\RouteHelpers;
use App\Helpers\WhatsAppHelper;
use App\Models\Announcement;
use App\Models\GymMember;
use App\Services\WhatsAppGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Pengumuman & Pengingat Membership
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/announcements', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today            = Carbon::today();
    $sevenDaysFromNow = $today->copy()->addDays(7);

    // Ambil pengumuman terbaru (tidak termasuk arsip)
    $announcements = Announcement::query()
        ->where('status', '!=', 'archived')
        ->latest('publish_at')
        ->latest()
        ->get();

    // Ambil pengumuman yang sudah diarsipkan
    $archivedAnnouncements = Announcement::query()
        ->where('status', 'archived')
        ->latest('archived_at')
        ->get();

    // PERBAIKAN: Hapus filter 'member_status' karena semua di tabel gym_members adalah member tetap
    $expiringMembers = GymMember::query()
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '>', $today)
        ->whereDate('expires_at', '<=', $sevenDaysFromNow)
        ->orderBy('expires_at')
        ->orderBy('full_name')
        ->get();

    return view('admin.announcements', array_merge(RouteHelpers::pageMeta('announcements'), [
        'announcements'   => $announcements,
        'archivedAnnouncements' => $archivedAnnouncements,
        'expiringMembers' => $expiringMembers,
        'whatsAppChannelUrl' => config('services.whatsapp.channel_url'),
        'whatsAppChannelShare' => session('whatsapp_channel_share'),
        'whatsAppDispatch' => session('whatsapp_dispatch'),
    ]));
})->name('announcements');

// ── Publish langsung ──────────────────────────────────────────────────────────
Route::post('/announcements/publish', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'body'  => ['required', 'string'],
    ]);

    $announcement = Announcement::create([
        'title'      => $validated['title'],
        'body'       => $validated['body'],
        'status'     => 'active',
        'publish_at' => now(),
    ]);

    $message = WhatsAppHelper::announcementMessage($announcement->title, $announcement->body);
    $channelUrl = config('services.whatsapp.channel_url');
    $statusMessage = 'Pengumuman baru berhasil dipublikasikan. Lanjutkan posting lewat WhatsApp Share.';

    return back()
        ->with('status', $statusMessage)
        ->with('whatsapp_channel_share', [
            'title' => $announcement->title,
            'message' => $message,
            'url' => $channelUrl,
            'share_url' => 'https://wa.me/?text='.rawurlencode($message),
            'auto_open' => true,
        ]);
})->name('announcements.publish');

// ── Jadwalkan ─────────────────────────────────────────────────────────────────
// ── Arsipkan ──────────────────────────────────────────────────────────────────
Route::post('/announcements/archive', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'announcement_id' => ['required', 'exists:announcements,id'],
    ]);

    Announcement::query()
        ->whereKey($validated['announcement_id'])
        ->update([
            'status' => 'archived',
            'archived_at' => now()
        ]);

    return back()->with('status', 'Pengumuman berhasil dipindahkan ke arsip.');
})->name('announcements.archive');

// ── Restore dari arsip ────────────────────────────────────────────────────────
Route::post('/announcements/restore', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'announcement_id' => ['required', 'exists:announcements,id'],
    ]);

    Announcement::query()
        ->whereKey($validated['announcement_id'])
        ->update([
            'status' => 'active',
            'archived_at' => null
        ]);

    return back()->with('status', 'Pengumuman berhasil dipulihkan dari arsip.');
})->name('announcements.restore');

// ── Hapus pengumuman ──────────────────────────────────────────────────────────
Route::post('/announcements/delete', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'announcement_id' => ['required', 'exists:announcements,id'],
    ]);

    Announcement::query()
        ->whereKey($validated['announcement_id'])
        ->delete();

    return back()->with('status', 'Pengumuman berhasil dihapus.');
})->name('announcements.delete');

Route::post('/announcements/reminders', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'gym_member_id' => ['required', 'exists:gym_members,id'],
    ]);

    $member = GymMember::query()->findOrFail($validated['gym_member_id']);
    $daysLeft = $member->expires_at
        ? (int) Carbon::today()->diffInDays($member->expires_at->copy()->startOfDay(), false)
        : null;

    if ($daysLeft === null || $daysLeft < 0 || $daysLeft > 7) {
        return back()->withErrors(['gym_member_id' => 'Pengingat hanya bisa dikirim untuk member yang masa aktifnya tersisa 0 sampai 7 hari.']);
    }

    $message = "Halo {$member->full_name}, masa aktif membership Anda akan segera berakhir pada " . ($member->expires_at ? $member->expires_at->format('d M Y') : '-') . ". Silakan datang ke kasir untuk melakukan perpanjangan membership.";

    // Update last reminder timestamp
    $member->update(['last_membership_reminder_at' => now()]);

    // Prepare announcement record
    Announcement::create([
        'title' => 'Pengingat Membership',
        'body' => "[TARGET_MEMBER_ID:{$member->id}] {$message}",
        'status' => 'active',
        'publish_at' => now(),
    ]);

    $statusMessage = "Pengingat perpanjangan untuk {$member->full_name} berhasil dikirim ke halaman member.";

    return back()->with('status', $statusMessage);
})->name('announcements.reminders.send');
