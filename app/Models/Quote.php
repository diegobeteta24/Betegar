<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
     protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'customer_id',
        'total',
        'observation',
    ];

    protected $casts = [
        'date' => 'datetime',
        
    ];
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
        ->withPivot('quantity', 'price', 'subtotal')
        ->withTimestamps();
    }
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}
}
