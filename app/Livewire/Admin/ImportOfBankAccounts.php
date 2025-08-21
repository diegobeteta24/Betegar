<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankAccountsImport;

class ImportOfBankAccounts extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'bank_name' => 'Banco Ejemplo',
            'account_name' => 'Cuenta Operaciones',
            'account_number' => '1234567890',
            'currency' => 'GTQ',
            'initial_balance' => 0,
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return \Maatwebsite\Excel\Facades\Excel::download($export,'bank_accounts_template.xlsx');
    }

    public function importBankAccounts()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $import = new BankAccountsImport();
        Excel::import($import, $this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();

        if (count($this->errors) === 0) {
            return redirect()->route('admin.bank-accounts.index')
                ->with('sweet-alert', [
                    'icon' => 'success',
                    'title'=> '¡Cuentas importadas!',
                    'text' => "Se han importado {$this->importedCount} cuentas bancarias correctamente.",
                    'timer'=> 3000,
                    'showConfirmButton' => false,
                ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-bank-accounts');
    }
}
