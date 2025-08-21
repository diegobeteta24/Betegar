<?php

namespace App\Imports;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class WorkOrdersImport implements ToCollection, WithHeadingRow
{
    protected array $errors = [];
    protected int $imported = 0;

    public function collection(Collection $rows)
    {
        foreach($rows as $i => $row){
            if ($row->filter()->isEmpty()) { continue; }

            $data = [
                'customer_id' => $row['customer_id'],
                'address' => $row['address'],
                'objective' => $row['objective'],
                'status' => $row['status'] ?? 'pending',
                'technicians' => $row['technicians'] ? array_filter(array_map('trim', explode('|', $row['technicians']))) : [],
            ];

            $validator = Validator::make($data, [
                'customer_id' => 'required|exists:customers,id',
                'address' => 'required|string|max:255',
                'objective' => 'required|string',
                'status' => 'nullable|in:pending,in_progress,done,cancelled',
                'technicians' => 'required|array|min:1',
            ], [], [
                'customer_id' => 'Cliente',
                'address' => 'Dirección',
                'objective' => 'Objetivo',
                'status' => 'Estado',
                'technicians' => 'Técnicos',
            ]);

            if ($validator->fails()) {
                $this->errors[] = 'Fila '.($i+2).': '. implode(' | ',$validator->errors()->all());
                continue;
            }

            try {
                $order = WorkOrder::create([
                    'customer_id' => $data['customer_id'],
                    'user_id' => $data['technicians'][0],
                    'address' => $data['address'],
                    'objective' => $data['objective'],
                    'status' => $data['status'] ?? 'pending',
                ]);
                $order->technicians()->sync($data['technicians']);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = 'Fila '.($i+2).': '.$e->getMessage();
            }
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->imported; }
}
