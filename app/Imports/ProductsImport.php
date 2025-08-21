<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithHeadingRow
{
    private array $errors= [];
    private int $importedCount = 0;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            $data = $row->toArray();

            $validator= Validator::make($data, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
            ]);
            if ($validator->fails()) {
                // Handle validation errors
                $this->errors[] = [
                    'row' => $index + 1, // +1 to match Excel row numbers
                    'errors' => $validator->errors()->all(),
                ];
                continue; // Skip this row if validation fails
            }
            
         $data['type'] = 'product';
         Product::create($data);
         $this->importedCount++;


        }
    }
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
