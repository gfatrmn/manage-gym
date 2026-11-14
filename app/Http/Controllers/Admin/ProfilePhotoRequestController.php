<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilePhotoChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfilePhotoRequestController extends Controller
{
    public function index(): Response
    {
        $requests = ProfilePhotoChangeRequest::query()
            ->with('member')
            ->latest()
            ->paginate(12)
            ->through(fn (ProfilePhotoChangeRequest $item) => [
                'id' => $item->id,
                'status' => $item->status,
                'member_name' => $item->member?->full_name ?? 'Member dihapus',
                'member_email' => $item->member?->email ?? '-',
                'member_current_photo' => $item->member?->profile_photo_url,
                'requested_photo_url' => route('admin.profile-photo-requests.photo', $item->id),
                'created_at' => $item->created_at?->translatedFormat('d M Y, H:i'),
                'reviewed_at' => $item->reviewed_at?->translatedFormat('d M Y, H:i'),
                'reviewed_by' => $item->reviewed_by,
            ]);

        return Inertia::render('Admin/ProfilePhotoRequests', [
            'pageTitle' => 'Persetujuan Foto Profil - Admin Arena Gym',
            'activePage' => 'profile-photo-requests',
            'requests' => $requests,
        ]);
    }

    public function showPhoto(ProfilePhotoChangeRequest $photoRequest): BinaryFileResponse
    {
        $relativePath = ltrim(str_replace(['..', '\\'], '', (string) $photoRequest->requested_photo_path), '/');
        $path = storage_path('app/public/' . $relativePath);

        if (! is_file($path)) {
            $legacyPath = base_path('user/storage/app/public/' . $relativePath);
            if (is_file($legacyPath)) {
                $path = $legacyPath;
            }
        }

        abort_unless(is_file($path), 404);

        return response()->file($path);
    }

    public function approve(ProfilePhotoChangeRequest $photoRequest): RedirectResponse
    {
        if ($photoRequest->status !== 'pending') {
            return redirect()->route('admin.profile-photo-requests')->with('status', 'Permintaan ini sudah diproses.');
        }

        $member = $photoRequest->member;
        if (! $member) {
            return redirect()->route('admin.profile-photo-requests')->withErrors(['member' => 'Data member tidak ditemukan.']);
        }

        DB::transaction(function () use ($photoRequest, $member) {
            if ($member->profile_photo_path) {
                Storage::disk('public')->delete($member->profile_photo_path);
            }

            $member->update([
                'profile_photo_path' => $photoRequest->requested_photo_path,
                'profile_photo_change_count' => (int) ($member->profile_photo_change_count ?? 3) + 1,
            ]);

            $photoRequest->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => (string) (session('auth.name') ?? session('auth.login') ?? 'admin'),
            ]);
        });

        return redirect()->route('admin.profile-photo-requests')->with('status', 'Foto profil member berhasil disetujui dan diganti.');
    }

    public function reject(ProfilePhotoChangeRequest $photoRequest): RedirectResponse
    {
        if ($photoRequest->status === 'pending') {
            if ($photoRequest->requested_photo_path) {
                Storage::disk('public')->delete($photoRequest->requested_photo_path);
            }

            $photoRequest->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => (string) (session('auth.name') ?? session('auth.login') ?? 'admin'),
            ]);
        }

        return redirect()->route('admin.profile-photo-requests')->with('status', 'Permintaan ganti foto profil ditolak.');
    }
}

