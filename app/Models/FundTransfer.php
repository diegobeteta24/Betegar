<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'technician_id',
    'bank_account_id',
        'amount',
        'currency',
        'note',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
