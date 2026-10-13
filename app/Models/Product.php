<?php

namespace App\Models;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=
    [
        'name',
        'description',
        'category_id',
        'cost',
        'price',
        'stock',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(){
        return $this->hasMany(Product::class);
    }
}
