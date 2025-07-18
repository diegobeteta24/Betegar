<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
     protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchaseOrder()
{
    return $this->belongsTo(PurchaseOrder::class);
}

public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}
}
