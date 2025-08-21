<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'initial_balance',
        'currency',
        'description',
    ];

    /**
     * Get all transactions for this account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    /**
     * Compute current balance: initial + credits – debits.
     */
    public function getCurrentBalanceAttribute(): float
    {
        return $this->initial_balance
             + $this->transactions()
                   ->sum(\DB::raw("CASE WHEN type = 'credit' THEN amount ELSE -amount END"));
    }
}