<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'category_id',
        'brand',
        'sku',
        'price',
        'stock',
        'unit',
        'description',
        'is_active',
    ];

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
