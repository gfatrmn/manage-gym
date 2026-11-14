<?php

namespace App\Services;

use App\Models\GymMember;
use App\Models\ProfilePhotoChangeRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MemberProfilePhotoService
{
    public const MAX_DIRECT_CHANGES = 3;

    /**
     * Memproses upload foto profil member dengan batas 3x ganti langsung,
     * selebihnya masuk antrean persetujuan admin.
     *
     * @return array{ok: bool, message: string, error?: string}
     */
    public function handleUpload(User $user, GymMember $member, UploadedFile $file): array
    {
        $changeCount = (int) ($member->profile_photo_change_count ?? 0);

        if ($changeCount < self::MAX_DIRECT_CHANGES) {
            if ($member->profile_photo_path) {
                Storage::disk('public')->delete($member->profile_photo_path);
            }

            $member->profile_photo_path = $file->store('member-profile-photos', 'public');
            $member->profile_photo_change_count = $changeCount + 1;
            $member->save();

            $remaining = max(0, self::MAX_DIRECT_CHANGES - $member->profile_photo_change_count);

            return [
                'ok' => true,
                'message' => "Foto profil berhasil diganti. Sisa ganti langsung: {$remaining}x.",
            ];
        }

        $hasPendingRequest = ProfilePhotoChangeRequest::query()
            ->where('gym_member_id', $member->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return [
                'ok' => false,
                'message' => 'Permintaan ganti foto profil Anda masih menunggu persetujuan admin.',
                'error' => 'Permintaan ganti foto profil Anda masih menunggu persetujuan admin.',
            ];
        }

        $requestPath = $file->store('member-profile-photo-requests', 'public');

        ProfilePhotoChangeRequest::query()->create([
            'user_id' => $user->id,
            'gym_member_id' => $member->id,
            'requested_photo_path' => $requestPath,
            'status' => 'pending',
        ]);

        return [
            'ok' => true,
            'message' => 'Jatah ganti foto profil sudah habis. Foto baru dikirim ke admin untuk persetujuan.',
        ];
    }
}

