<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Table;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'table_id',
        'user_id',
        'sub_total',
        'descount',
        'total',
        'status',
        'payment_status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}