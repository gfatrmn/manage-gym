<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'package_id',
        'transaction_id',
        'start_date',
        'end_date',
        'price',
        'status',
        'status_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'member_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class, 'package_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CashierTransaction::class, 'transaction_id');
    }
}
