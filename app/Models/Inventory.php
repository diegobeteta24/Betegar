<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
     protected $fillable = [
        'detail',
        'warehouse_id',
        'quantity_in',
        'cost_in',
        'total_in',
        'quantity_out',
        'cost_out',
        'total_out',
        'quantity_balance',
         'cost_balance',
        'total_balance',
        'product_id',
        'warehouse_id',
        'inventoryable_id',
        'inventoryable_type',
    ];

    public function warehouse()
{
    return $this->belongsTo(Warehouse::class);
}

public function inventoryable()
{
    return $this->morphTo();
}

public function product()
{
    return $this->belongsTo(Product::class);
}

}
