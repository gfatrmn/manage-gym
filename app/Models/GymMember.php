<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'user_id',
        'email',
        'phone',
        'member_status',
        'membership_plan',
        'package_status',
        'guest_visit_type',
        'can_check_in',
        'joined_at',
        'expires_at',
        'last_membership_reminder_at',
        'status',
        'checkin_code',
        'profile_photo_path',
        'profile_photo_change_count',
        'payment_method',
        'payment_amount',
        'visit_date',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'expires_at' => 'date',
        'last_membership_reminder_at' => 'datetime',
        'visit_date' => 'date',
        'can_check_in' => 'boolean',
        'profile_photo_change_count' => 'integer',
    ];

    // Accessor Inisial Nama
    public function getProfileInitialsAttribute()
    {
        return collect(explode(' ', $this->full_name))
            ->map(fn($n) => mb_substr($n, 0, 1))
            ->take(2)
            ->join('');
    }

    // Accessor URL Foto
    public function getProfilePhotoUrlAttribute()
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        $path = ltrim($this->profile_photo_path, '/');
        $userStorageExists = file_exists(base_path('user/storage/app/public/' . $path));
        $rootStorageExists = file_exists(storage_path('app/public/' . $path));

        return ($userStorageExists || $rootStorageExists)
            ? route('member.profile-photo.show', ['path' => $path])
            : asset('storage/' . $path);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(GymCheckin::class, 'gym_member_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifiedCheckins(): HasMany
    {
        return $this->checkins()
            ->where('verification_status', 'verified')
            ->latest('checked_in_at');
    }

    public function productTransactions(): HasMany
    {
        return $this->hasMany(CashierTransaction::class, 'gym_member_id')
            ->whereNotNull('product_id')
            ->latest('transaction_at');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MemberHistory::class, 'gym_member_id')
            ->latest('occurred_at');
    }

    public function checkinHistories(): HasMany
    {
        return $this->histories()->where('history_type', 'checkin');
    }

    public function productPurchaseHistories(): HasMany
    {
        return $this->histories()->where('history_type', 'product_purchase');
    }
}
