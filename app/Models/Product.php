<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const LOW_STOCK_THRESHOLD = 10;

    protected $fillable =
    [
        'name',
        'description',
        'category_id',
        'cost',
        'price',
        'stock',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('status', true)
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD);
    }
}
