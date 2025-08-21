<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\BankTransaction;

class PurchaseObserver
{
    public function created(Purchase $purchase): void
    {
        $this->createDebit($purchase);
    }

    public function updated(Purchase $purchase): void
    {
        if ($purchase->wasChanged(['total'])) {
            // avoid duplicate: simplistic approach (only create once at first creation)
            return;
        }
    }

    protected function createDebit(Purchase $purchase): void
    {
        // Only if bank_account_id & total exist (fields might differ, adapt if needed)
        if (!isset($purchase->bank_account_id) || !$purchase->bank_account_id) {
            return; // no linked bank account
        }
        if ($purchase->total <= 0) {
            return;
        }
        $exists = BankTransaction::where('transactionable_type', Purchase::class)
            ->where('transactionable_id', $purchase->id)
            ->exists();
        if ($exists) return;

        BankTransaction::create([
            'bank_account_id'      => $purchase->bank_account_id,
            'transactionable_type' => Purchase::class,
            'transactionable_id'   => $purchase->id,
            'type'                 => 'debit',
            'date'                 => now(),
            'amount'               => $purchase->total,
            'description'          => 'Compra #'.$purchase->id,
        ]);
    }
}
