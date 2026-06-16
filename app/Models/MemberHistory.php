<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_member_id',
        'product_id',
        'history_type',
        'occurred_at',
        'title',
        'description',
        'quantity',
        'amount',
        'source_type',
        'source_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'quantity' => 'integer',
            'amount' => 'integer',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'gym_member_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
