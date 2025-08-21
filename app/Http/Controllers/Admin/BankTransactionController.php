<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    /**
     * Display a listing of the transactions, optionally filtered by account.
     */
    public function index(Request $request)
    {
        $accountId = $request->integer('account_id');
        $accounts = BankAccount::orderBy('name')->get(['id','name','currency']);

        $query = BankTransaction::with(['account', 'transactionable'])
            ->orderByDesc('date')
            ->orderByDesc('id');
        if ($accountId) {
            $query->where('bank_account_id', $accountId);
        }
        $transactions = $query->paginate(25)->withQueryString();

        return view('admin.bank_transactions.index', compact('transactions', 'accounts', 'accountId'));
    }

    /**
     * Show form to create a manual bank transaction (NON sales): generic credit (ingreso) or expense (gasto) debit.
     */
    public function create()
    {
        $accounts = BankAccount::orderBy('name')->get(['id','name','currency']);
        return view('admin.bank_transactions.create', compact('accounts'));
    }

    /**
     * Store manual bank transaction.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => ['required','exists:bank_accounts,id'],
            'mode'            => ['required','in:income,expense'], // income = crédito libre, expense = débito
            'amount'          => ['required','numeric','min:0.01'],
            'description'     => ['nullable','string','max:255'],
        ]);

        return DB::transaction(function() use ($data) {
            $account = BankAccount::lockForUpdate()->findOrFail($data['bank_account_id']);

            BankTransaction::create([
                'bank_account_id'      => $account->id,
                'transactionable_id'   => null,
                'transactionable_type' => null,
                'type'                 => $data['mode'] === 'income' ? 'credit' : 'debit',
                'date'                 => now(),
                'amount'               => $data['amount'],
                'description'          => $data['description'] ?: ($data['mode'] === 'income' ? 'Ingreso manual' : 'Gasto manual'),
            ]);

            return redirect()->route('admin.bank-transactions.index')
                ->with('sweet-alert', [
                    'icon' => 'success',
                    'title'=> 'Movimiento registrado',
                    'timer'=> 2200,
                    'showConfirmButton' => false,
                ]);
        });
    }
}
