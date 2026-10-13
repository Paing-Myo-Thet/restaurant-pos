<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Mark the table occupied while it has open orders, and free it once
     * they are all closed. Reserved tables stay reserved until an order starts.
     */
    public function syncStatus(): void
    {
        if ($this->sales()->open()->exists()) {
            $this->update(['status' => 'occupied']);
        } elseif ($this->status === 'occupied') {
            $this->update(['status' => 'available']);
        }
    }
}
