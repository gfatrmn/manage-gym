<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'status',
        'publish_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }
}
