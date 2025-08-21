<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use App\Models\Quote;
use Carbon\Carbon;

class QuotesImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach($rows as $index => $row){
            $data = $row->toArray();
            $validator = Validator::make($data,[
                'voucher_type' => 'required|string|max:50',
                'serie' => 'nullable|string|max:20',
                'correlative' => 'nullable|string|max:50',
                'date' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'subtotal' => 'required|numeric',
                'discount_percent' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric',
                'total' => 'required|numeric',
                'observation' => 'nullable|string',
            ]);
            if($validator->fails()){
                $this->errors[] = [
                    'row' => $index+1,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }
            // Normalize defaults
            $data['discount_percent'] = $data['discount_percent'] ?? 0;
            $data['discount_amount'] = $data['discount_amount'] ?? 0;
            $data['date'] = Carbon::parse($data['date']);

            Quote::create($data);
            $this->importedCount++;
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->importedCount; }
}
