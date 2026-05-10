<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GymMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'joined_at',
        'expires_at',
        'status',
        'checkin_code',
        'profile_photo_path',
        'payment_method'
    ];

    protected $casts = [
        'joined_at' => 'date',
        'expires_at' => 'date',
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
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }
}
