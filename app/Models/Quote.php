<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'customer_id',
    'customer_address_id',
    'subtotal',
    'discount_percent',
    'discount_amount',
        'total',
        'observation',
    'public_token',
    ];

    protected $casts = [
        'date' => 'datetime',
        
    ];
    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
    ->withPivot('quantity', 'price', 'subtotal', 'description')
        ->withTimestamps();
    }
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    // Relación 1:1 inversa para poder excluir rápidamente cotizaciones ya convertidas en venta
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

public function inventories()
{
    return $this->morphMany(Inventory::class, 'inventoryable');
}
}
