<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
     protected $fillable = [
        'type',
        'serie',
        'correlative',
        'date',
        'warehouse_id',
        'total',
        'observation',
        'reason_id',
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
    public function reason()
{
    return $this->belongsTo(Reason::class);
}

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}
}
