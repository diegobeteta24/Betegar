<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'transactionable_id',
        'transactionable_type',
        'type',
        'date',
        'amount',
        'description',
        'category_id',
        'origin_type',
        'origin_id',
    ];

    protected $casts = [
        'date'   => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * The bank account this transaction belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /**
     * The parent model (Sale, Purchase, or ManualExpense).
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
