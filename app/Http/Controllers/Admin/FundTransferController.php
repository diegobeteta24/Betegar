<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransfer;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class FundTransferController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', FundTransfer::class);
        return FundTransfer::with(['admin','technician'])->orderBy('sent_at','desc')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('create', FundTransfer::class);
        $data = $request->validate([
            'technician_id' => ['required','exists:users,id'],
            'bank_account_id' => ['required','exists:bank_accounts,id'],
            'amount' => ['required','numeric','min:0.01'],
            'currency' => ['nullable','string','size:3'],
            'note' => ['nullable','string','max:255'],
        ]);
        $data['currency'] = $data['currency'] ?? 'GTQ';
        $data['admin_id'] = $request->user()->id;
        $data['sent_at'] = now('UTC');
        $transfer = FundTransfer::create($data);

        // Registrar transacción bancaria (débito)
        \App\Models\BankTransaction::create([
            'bank_account_id' => $data['bank_account_id'],
            'transactionable_id' => $transfer->id,
            'transactionable_type' => FundTransfer::class,
            'type' => 'debit',
            'date' => now('UTC'),
            'amount' => $data['amount'],
            'description' => 'Fondos enviados a técnico #'.$data['technician_id'],
            'origin_type' => 'fund_transfer',
            'origin_id' => $transfer->id,
        ]);

        return $transfer->load(['admin','technician','bankAccount']);
    }

    public function destroy(Request $request, FundTransfer $fundTransfer)
    {
        $this->authorize('delete', $fundTransfer);
        // Encontrar transacción bancaria asociada (debit) y crear reverso credit
        if($fundTransfer->bank_account_id){
            $orig = BankTransaction::where('transactionable_type', FundTransfer::class)
                ->where('transactionable_id', $fundTransfer->id)
                ->first();
            if($orig){
                BankTransaction::create([
                    'bank_account_id' => $fundTransfer->bank_account_id,
                    'transactionable_id' => $fundTransfer->id,
                    'transactionable_type' => FundTransfer::class,
                    'type' => 'credit',
                    'date' => now('UTC'),
                    'amount' => $fundTransfer->amount,
                    'description' => 'Reverso de envío a técnico #'.$fundTransfer->technician_id,
                    'origin_type' => 'fund_transfer_reversal',
                    'origin_id' => $fundTransfer->id,
                ]);
            }
        }
        $fundTransfer->delete();
        return response()->json(['deleted'=>true]);
    }
}
