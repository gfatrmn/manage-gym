<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'gym_member_id',
        'product_id',
        'customer_name',
        'transaction_group',
        'transaction_type',
        'amount',
        'paid_amount',
        'change_amount',
        'quantity',
        'payment_method',
        'payment_status',
        'receipt_status',
        'transaction_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_amount' => 'integer',
            'change_amount' => 'integer',
            'quantity' => 'integer',
            'transaction_at' => 'datetime',
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
