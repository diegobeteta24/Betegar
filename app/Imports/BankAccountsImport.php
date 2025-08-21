<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Validator;

class BankAccountsImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();
            $validator = Validator::make($data, [
                'bank_name'    => 'required|string|max:255',
                'account_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:100|unique:bank_accounts,account_number',
                'currency'     => 'nullable|string|max:10',
                'initial_balance' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }
            BankAccount::create([
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'currency' => $data['currency'] ?? 'GTQ',
                'initial_balance' => $data['initial_balance'] ?? 0,
                'current_balance' => $data['initial_balance'] ?? 0,
            ]);
            $this->importedCount++;
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->importedCount; }
}
