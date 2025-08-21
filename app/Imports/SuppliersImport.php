<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

class SuppliersImport implements ToCollection, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();

            $validator = Validator::make($data, [
                'identity_id'     => 'required|exists:identities,id',
                'document_number' => 'required|string|max:50',
                'name'            => 'required|string|max:255',
                'address'         => 'nullable|string|max:255',
                'email'           => 'nullable|email|max:255',
                'phone'           => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $identityId = (int) ($data['identity_id'] ?? 0);
            $doc = strtoupper($data['document_number'] ?? '');

            if ($identityId === 1 && $doc !== 'CF') {
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => ["Cuando la identidad es 'Sin documento', el número debe ser 'CF'."],
                ];
                continue;
            }
            if ($identityId !== 1 && $doc === 'CF') {
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => ["'CF' sólo está permitido si no posee documento."],
                ];
                continue;
            }
            if ($identityId === 2 && Supplier::where('document_number', $data['document_number'])->exists()) { // DPI único
                $this->errors[] = [
                    'row'    => $index + 1,
                    'errors' => ["Ya existe un proveedor con este DPI."],
                ];
                continue;
            }
            // NIT (id=3) duplicados permitidos

            Supplier::create($data);
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
