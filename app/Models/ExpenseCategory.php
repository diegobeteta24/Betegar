<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * (Optional) If you later create a ManualExpense model,
     * you can define:
     *
     * public function manualExpenses(): HasMany
     * {
     *     return $this->hasMany(ManualExpense::class);
     * }
     */
}
