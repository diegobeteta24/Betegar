<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
     protected $fillable = [
        'serie',
        'correlative',
        'date',
        'total',
        'observation',
        'origin_warehouse_id',
        'destination_warehouse_id',
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
    public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}

public function originWarehouse()
{
    return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
}

public function destinationWarehouse()
{
    return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
}

}
