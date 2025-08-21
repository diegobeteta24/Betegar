<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the bank accounts.
     */
    public function index()
    {
        Gate::authorize('bank-account.view', BankAccount::class);
        return view('admin.bank_accounts.index');
    }

    /**
     * Show the form for creating a new bank account.
     */
    public function create()
    {
        Gate::authorize('bank-account.create', BankAccount::class);
        // The Livewire component in the view will handle the actual creation logic
        return view('admin.bank_accounts.create');
    }

    /**
     * Show the form for editing an existing bank account.
     */
    public function edit(BankAccount $bankAccount)
    {
        Gate::authorize('bank-account.update', $bankAccount);
        // Pass the model to Livewire via the view
        return view('admin.bank_accounts.edit', compact('bankAccount'));
    }

    /**
     * Formulario para importación masiva de cuentas bancarias vía Excel.
     */
    public function import(Request $request)
    {
        Gate::authorize('bank-account.create', BankAccount::class);
        return view('admin.bank_accounts.import');
    }
}
