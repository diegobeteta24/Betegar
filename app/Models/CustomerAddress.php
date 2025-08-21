<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'label',
        'address',
        'is_primary',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
