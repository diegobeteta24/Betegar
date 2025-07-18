<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\Inventory;
use App\Models\Image;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;



class Product extends Model
{
    use HasFactory;
    
      protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'price',
        'category_id',
        'stock',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->images->count() ? Storage::url($this->images->first()->path) : asset('images/no-image.jpg'),
        );
    }

    public function category()
{
    return $this->belongsTo(Category::class);
}

// items polimórficos (quotes, sales, purchases…)

public function purchaseOrders()
{
    return $this->morphedByMany(PurchaseOrder::class, 'productable');
}

public function quotes()
{
    return $this->morphedByMany(Quote::class, 'productable');
}

public function inventories()
{
    return $this->hasMany(Inventory::class);
}


public function images()
{
    return $this->morphMany(Image::class, 'imageable');
}

protected static function booted()
    {
        static::deleting(function (Product $product) {
            // 1) Borra cada fichero del disco 'public'
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
            }

            // 2) Borra los registros de la BD
            //    Con morphMany, usas delete() para barrerlos
            $product->images()->delete();
        });
    }

}
