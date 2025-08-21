<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_id',
    'expense_category_id',
        'description',
        'amount',
        'has_voucher',
    ];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
