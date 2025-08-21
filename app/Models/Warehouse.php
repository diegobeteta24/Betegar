<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
