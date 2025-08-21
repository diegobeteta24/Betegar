<?php

namespace App\Imports;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PurchaseOrdersImport implements ToCollection, WithHeadingRow
{
    protected array $errors = [];
    protected int $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            if ($row->filter()->isEmpty()) { continue; }

            $data = [
                'date' => $row['date'],
                'supplier_id' => $row['supplier_id'],
                'warehouse_id' => $row['warehouse_id'],
                'total' => $row['total'],
                'observation' => $row['observation'] ?? null,
            ];

            $validator = Validator::make($data, [
                'date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'total' => 'required|numeric|min:0',
                'observation' => 'nullable|string',
            ], [], [
                'date' => 'Fecha',
                'supplier_id' => 'Proveedor',
                'warehouse_id' => 'Almacén',
                'total' => 'Total',
                'observation' => 'Observación',
            ]);

            if ($validator->fails()) {
                $this->errors[] = 'Fila '.($i+2).': '. implode(' | ', $validator->errors()->all());
                continue;
            }

            try {
                PurchaseOrder::create($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = 'Fila '.($i+2).': '.$e->getMessage();
            }
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->imported; }
}
