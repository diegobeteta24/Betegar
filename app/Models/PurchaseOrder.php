<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'supplier_id',
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
   
    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

public function purchases()
{
    return $this->hasMany(Purchase::class);
}

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}

}
