<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\BankAccount;

class BankAccountCreate extends Component
{
    public $name;
    public $initial_balance = 0;
    public $currency = 'GTQ';
    public $description;

    public function boot()
    {
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $html = "<ul class='text-left'>";
                foreach ($errors as $error) {
                    $html .= "<li>{$error[0]}</li>";
                }
                $html .= "</ul>";
                $this->dispatch('swal', [
                    'title' => 'Validation Errors',
                    'html'  => $html,
                    'icon'  => 'error',
                ]);
            }
        });
    }

    public function save()
    {
        $this->validate([
            'name'            => 'required|string|unique:bank_accounts,name',
            'initial_balance' => 'required|numeric|min:0',
            'currency'        => 'required|string|size:3',
            'description'     => 'nullable|string|max:255',
        ], [], [
            'name'            => 'Account Name',
            'initial_balance' => 'Initial Balance',
            'currency'        => 'Currency',
            'description'     => 'Description',
        ]);

        BankAccount::create([
            'name'            => $this->name,
            'initial_balance' => $this->initial_balance,
            'currency'        => strtoupper($this->currency),
            'description'     => $this->description,
        ]);

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('sweet-alert', [
                'icon'    => 'success',
                'title'   => 'Account Created',
                'text'    => 'The bank account has been added successfully.',
                'timer'   => 3000,
                'showConfirmButton' => false,
            ]);
    }

    public function render()
    {
        return view('livewire.admin.bank-account-create');
    }
}
