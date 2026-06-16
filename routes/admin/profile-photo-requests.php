<?php

use App\Helpers\RouteHelpers;
use App\Models\ProfilePhotoChangeRequest;
use Illuminate\Support\Facades\Route;

Route::get('/profile-photo-requests', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    return view('admin.profile-photo-requests', [
        'pageTitle' => 'Persetujuan Foto Profil - Admin Arena Gym',
        'activePage' => 'profile-photo-requests',
        'requests' => ProfilePhotoChangeRequest::query()
            ->with('member')
            ->latest()
            ->paginate(12),
    ]);
})->name('profile-photo-requests');

Route::get('/profile-photo-requests/{photoRequest}/photo', function (ProfilePhotoChangeRequest $photoRequest) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $path = base_path('user/storage/app/public/' . $photoRequest->requested_photo_path);

    abort_unless(is_file($path), 404);

    return response()->file($path);
})->name('profile-photo-requests.photo');

Route::post('/profile-photo-requests/{photoRequest}/approve', function (ProfilePhotoChangeRequest $photoRequest) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    if ($photoRequest->status !== 'pending') {
        return redirect()->route('admin.profile-photo-requests')->with('status', 'Permintaan ini sudah diproses.');
    }

    $member = $photoRequest->member;

    if (! $member) {
        return redirect()->route('admin.profile-photo-requests')->withErrors(['member' => 'Data member tidak ditemukan.']);
    }

    if ($member->profile_photo_path) {
        $oldPath = base_path('user/storage/app/public/' . $member->profile_photo_path);

        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
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

    return redirect()->route('admin.profile-photo-requests')->with('status', 'Foto profil member berhasil disetujui dan diganti.');
})->name('profile-photo-requests.approve');

Route::post('/profile-photo-requests/{photoRequest}/reject', function (ProfilePhotoChangeRequest $photoRequest) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    if ($photoRequest->status === 'pending') {
        $photoRequest->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => (string) (session('auth.name') ?? session('auth.login') ?? 'admin'),
        ]);
    }

    return redirect()->route('admin.profile-photo-requests')->with('status', 'Permintaan ganti foto profil ditolak.');
})->name('profile-photo-requests.reject');
