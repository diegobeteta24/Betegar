<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoriesImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();
            $validator = Validator::make($data, [
                'name' => 'required|string|max:100|unique:expense_categories,name'
            ]);
            if ($validator->fails()) {
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }
            ExpenseCategory::create(['name' => $data['name']]);
            $this->importedCount++;
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->importedCount; }
}
