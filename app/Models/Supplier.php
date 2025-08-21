<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use SoftDeletes;
    use HasFactory;
      protected $fillable = [
        'identity_id',
        'document_number',
        'name',
        'address',
        'email',
        'phone',
    ];
    public function identity()
{
    return $this->belongsTo(Identity::class);
}

public function purchaseOrders()
{
    return $this->hasMany(PurchaseOrder::class);
}

public function purchases()
{
    return $this->hasMany(Purchase::class);
}
}
