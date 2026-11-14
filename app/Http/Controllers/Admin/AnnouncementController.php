<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\WhatsAppHelper;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\GymMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);

        $announcements = Announcement::query()
            ->where('status', '!=', 'archived')
            ->latest('publish_at')
            ->latest()
            ->get()
            ->map(fn (Announcement $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'body' => trim(preg_replace('/^\[TARGET_MEMBER_ID:\d+\]\s*/', '', (string) $item->body)),
                'is_targeted' => str_starts_with((string) $item->body, '[TARGET_MEMBER_ID:'),
                'status' => $item->status,
                'publish_at' => $item->publish_at?->translatedFormat('d M Y, H:i') ?? '-',
            ]);

        $archivedAnnouncements = Announcement::query()
            ->where('status', 'archived')
            ->latest('archived_at')
            ->get()
            ->map(fn (Announcement $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'body' => trim(preg_replace('/^\[TARGET_MEMBER_ID:\d+\]\s*/', '', (string) $item->body)),
                'archived_at' => $item->archived_at?->translatedFormat('d M Y, H:i') ?? '-',
            ]);

        $expiringMembers = GymMember::query()
            ->where('status', 'member')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', $today)
            ->whereDate('expires_at', '<=', $sevenDaysFromNow)
            ->orderBy('expires_at')
            ->orderBy('full_name')
            ->get()
            ->map(fn (GymMember $member) => [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'phone' => $member->phone ?: '-',
                'expires_at' => $member->expires_at?->translatedFormat('d M Y') ?? '-',
                'days_left' => (int) $today->diffInDays($member->expires_at->copy()->startOfDay(), false),
                'last_reminder' => $member->last_membership_reminder_at?->translatedFormat('d M Y, H:i') ?? 'Belum pernah',
                'whatsapp_url' => WhatsAppHelper::buildUrl($member->phone, WhatsAppHelper::reminderMessage($member)),
            ]);

        return Inertia::render('Admin/Announcements', [
            'pageTitle' => 'Pengumuman - Arena Gym',
            'activePage' => 'announcements',
            'announcements' => $announcements,
            'archivedAnnouncements' => $archivedAnnouncements,
            'expiringMembers' => $expiringMembers,
            'whatsAppChannelUrl' => config('services.whatsapp.channel_url'),
        ]);
    }

    public function publish(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $announcement = Announcement::query()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => 'active',
            'publish_at' => now(),
        ]);

        $message = WhatsAppHelper::announcementMessage($announcement->title, $announcement->body);

        return back()
            ->with('status', 'Pengumuman baru berhasil dipublikasikan.')
            ->with('whatsapp_channel_share', [
                'title' => $announcement->title,
                'message' => $message,
                'url' => config('services.whatsapp.channel_url'),
                'share_url' => 'https://wa.me/?text=' . rawurlencode($message),
            ]);
    }

    public function archive(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'announcement_id' => ['required', 'exists:announcements,id'],
        ]);

        Announcement::query()
            ->whereKey($validated['announcement_id'])
            ->update([
                'status' => 'archived',
                'archived_at' => now(),
            ]);

        return back()->with('status', 'Pengumuman berhasil dipindahkan ke arsip.');
    }

    public function restore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'announcement_id' => ['required', 'exists:announcements,id'],
        ]);

        Announcement::query()
            ->whereKey($validated['announcement_id'])
            ->update([
                'status' => 'active',
                'archived_at' => null,
            ]);

        return back()->with('status', 'Pengumuman berhasil dipulihkan dari arsip.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'announcement_id' => ['required', 'exists:announcements,id'],
        ]);

        Announcement::query()->whereKey($validated['announcement_id'])->delete();

        return back()->with('status', 'Pengumuman berhasil dihapus.');
    }

    public function sendReminder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gym_member_id' => ['required', 'exists:gym_members,id'],
        ]);

        $member = GymMember::query()->findOrFail($validated['gym_member_id']);
        $daysLeft = $member->expires_at
            ? (int) Carbon::today()->diffInDays($member->expires_at->copy()->startOfDay(), false)
            : null;

        if ($daysLeft === null || $daysLeft < 0 || $daysLeft > 7) {
            return back()->withErrors([
                'gym_member_id' => 'Pengingat hanya bisa dikirim untuk member yang masa aktifnya tersisa 0 sampai 7 hari.',
            ]);
        }

        $message = "Halo {$member->full_name}, masa aktif membership Anda akan segera berakhir pada "
            . ($member->expires_at ? $member->expires_at->format('d M Y') : '-')
            . '. Silakan datang ke kasir untuk melakukan perpanjangan membership.';

        $member->update(['last_membership_reminder_at' => now()]);

        Announcement::query()->create([
            'title' => 'Pengingat Membership',
            'body' => "[TARGET_MEMBER_ID:{$member->id}] {$message}",
            'status' => 'active',
            'publish_at' => now(),
        ]);

        return back()->with('status', "Pengingat perpanjangan untuk {$member->full_name} berhasil dikirim ke portal member.");
    }
}

