<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymMember extends Model
{
    use HasFactory;

    protected $table = 'gym_members';

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'member_status',
        'membership_plan',
        'joined_at',
        'expires_at',
        'status',
        'checkin_code',
        'profile_photo_path',
        'profile_photo_change_count',
        'payment_method',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'expires_at' => 'date',
        'profile_photo_change_count' => 'integer',
    ];

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class, 'gym_member_id');
    }

    public function verifiedCheckins(): HasMany
    {
        return $this->checkins()
            ->where('verification_status', 'verified')
            ->latest('checked_in_at');
    }

    // Accessor URL Foto
    public function getProfilePhotoUrlAttribute()
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        $path = ltrim($this->profile_photo_path, '/');
        $userStorageExists = file_exists(storage_path('app/public/' . $path));
        $rootStorageExists = file_exists(base_path('../storage/app/public/' . $path));

        return ($userStorageExists || $rootStorageExists)
            ? route('member.profile-photo.show', ['path' => $path])
            : asset('storage/' . $path);
    }
}
