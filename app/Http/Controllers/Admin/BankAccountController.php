<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the bank accounts.
     */
    public function index()
    {
        // The Livewire component in the view will fetch & render the accounts
        return view('admin.bank_accounts.index');
    }

    /**
     * Show the form for creating a new bank account.
     */
    public function create()
    {
        // The Livewire component in the view will handle the actual creation logic
        return view('admin.bank_accounts.create');
    }

    /**
     * Show the form for editing an existing bank account.
     */
    public function edit(BankAccount $bankAccount)
    {
        // Pass the model to Livewire via the view
        return view('admin.bank_accounts.edit', compact('bankAccount'));
    }
}
