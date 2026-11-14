<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class GymMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'gender',
        'birth_date',
        'user_id',
        'email',
        'phone',
        'address',
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
        'birth_date' => 'date',
        'joined_at' => 'date',
        'expires_at' => 'date',
        'last_membership_reminder_at' => 'datetime',
        'visit_date' => 'date',
        'can_check_in' => 'boolean',
        'profile_photo_change_count' => 'integer',
        'payment_amount' => 'integer',
    ];

    protected $appends = [
        'profile_photo_url',
        'profile_initials',
    ];

    public function getProfileInitialsAttribute(): string
    {
        return collect(explode(' ', trim((string) $this->full_name)))
            ->filter()
            ->map(fn (string $n) => mb_strtoupper(mb_substr($n, 0, 1)))
            ->take(2)
            ->join('');
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        $path = ltrim($this->profile_photo_path, '/');

        if (Storage::disk('public')->exists($path) || is_file(storage_path('app/public/' . $path))) {
            return route('member.profile-photo.show', ['path' => $path]);
        }

        return asset('storage/' . $path);
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

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'member_id')->latest('start_date');
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class, 'member_id')
            ->where('status', 'active')
            ->latest('end_date');
    }

    public function productPurchaseHistories(): HasMany
    {
        return $this->histories()->where('history_type', 'product_purchase');
    }
}
