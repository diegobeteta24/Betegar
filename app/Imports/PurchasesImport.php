<?php

namespace App\Imports;

use App\Models\Purchase;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PurchasesImport implements ToCollection, WithHeadingRow
{
    protected array $errors = [];
    protected int $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            if ($row->filter()->isEmpty()) {
                continue; // skip empty
            }

            $data = [
                'voucher_type' => trim((string)$row['voucher_type']),
                'serie'        => trim((string)$row['serie']),
                'correlative'  => trim((string)$row['correlative']),
                'date'         => $row['date'],
                'purchase_order_id' => $row['purchase_order_id'],
                'supplier_id'  => $row['supplier_id'],
                'warehouse_id' => $row['warehouse_id'],
                'bank_account_id' => $row['bank_account_id'],
                'total'        => $row['total'],
                'observation'  => $row['observation'] ?? null,
            ];

            $validator = Validator::make($data, [
                'voucher_type' => 'required|string|max:10',
                'serie'        => 'nullable|string|max:10',
                'correlative'  => 'nullable|string|max:20',
                'date'         => 'required|date',
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',
                'supplier_id'  => 'required|exists:suppliers,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'bank_account_id' => 'nullable|exists:bank_accounts,id',
                'total'        => 'required|numeric|min:0',
                'observation'  => 'nullable|string',
            ], [], [
                'voucher_type' => 'Tipo Comprobante',
                'serie'        => 'Serie',
                'correlative'  => 'Correlativo',
                'date'         => 'Fecha',
                'purchase_order_id' => 'Orden de Compra',
                'supplier_id'  => 'Proveedor',
                'warehouse_id' => 'Almacén',
                'bank_account_id' => 'Cuenta Bancaria',
                'total'        => 'Total',
                'observation'  => 'Observación',
            ]);

            if ($validator->fails()) {
                $this->errors[] = 'Fila '.($i+2).': '. implode(' | ', $validator->errors()->all());
                continue;
            }

            try {
                Purchase::create($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = 'Fila '.($i+2).': '.$e->getMessage();
            }
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->imported; }
}
