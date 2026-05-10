<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyGuest extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'daily_guests';

    // Daftarkan kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'full_name',
        'phone',
        'payment_amount',
        'payment_method',
        'visit_at',
    ];

    // Pastikan visit_at dianggap sebagai objek Carbon/tanggal
    protected $casts = [
        'visit_at' => 'datetime',
    ];
}
