<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_member_id',
        'checked_in_at',
        'checkin_method',
        'verification_status',
        'submitted_name',
        'submitted_phone',
        'verified_at',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'gym_member_id');
    }
}
