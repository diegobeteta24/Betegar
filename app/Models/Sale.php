<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
     protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'quote_id',
     'customer_id',
      'warehouse_id',
    'subtotal',
    'discount_percent',
    'discount_amount',
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

public function quote()
{
    return $this->belongsTo(Quote::class);
}

public function warehouse()
{
    return $this->belongsTo(Warehouse::class);
}

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}
public function payments()
{
    return $this->hasMany(SalePayment::class);
}
public function getPaidAmountAttribute(): float
{
    return $this->payments()->sum('amount');
}
public function getDueAmountAttribute(): float
{
    return $this->total - $this->paid_amount;
}

}
