<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
     protected $fillable = [
        'path',
        'size',
        'tag',
        'imageable_id',
        'imageable_type',
    ];
    
    public function imageable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeSignature($query)
    {
        return $query->where('tag', 'signature');
    }
}
