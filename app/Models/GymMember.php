<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GymMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'profile_photo_path',
        'profile_photo_pending_path',
        'profile_photo_pending_status',
        'checkin_code',
        'member_status',
        'membership_plan',
        'package_status',
        'guest_visit_type',
        'payment_method',
        'payment_amount',
        'can_check_in',
        'visit_date',
        'joined_at',
        'expires_at',
        'last_membership_reminder_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'can_check_in' => 'boolean',
            'payment_amount' => 'integer',
            'visit_date' => 'date',
            'joined_at' => 'date',
            'expires_at' => 'date',
            'last_membership_reminder_at' => 'datetime',
        ];
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_photo_path);
    }

    public function getProfilePhotoPendingUrlAttribute(): ?string
    {
        if (! $this->profile_photo_pending_path) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_photo_pending_path);
    }

    public function getProfileInitialsAttribute(): string
    {
        return Str::of($this->full_name)
            ->trim()
            ->explode(' ')
            ->take(2)
            ->map(fn (string $part) => Str::substr($part, 0, 1))
            ->implode('');
    }
}
