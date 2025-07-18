<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use hasFactory;

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

public function quotes()
{
    return $this->hasMany(Quote::class);
}

public function sales()
{
    return $this->hasMany(Sale::class);
}
}
