<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipRenewalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gym_member_id',
        'plan',
        'duration_months',
        'amount',
        'payment_method',
        'payment_proof_path',
        'status',
        'requested_at',
    ];

    protected function casts(): array
    {
        return [
            'duration_months' => 'integer',
            'amount' => 'integer',
            'requested_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'gym_member_id');
    }
}
