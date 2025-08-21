<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ServicesImport implements ToCollection, WithHeadingRow
{
    private array $errors=[];
    private int $importedCount=0;

    public function collection(Collection $rows)
    {
        foreach($rows as $index=>$row){
            $data = $row->toArray();
            $validator = Validator::make($data,[
                'name'=>'required|string|max:255',
                'description'=>'nullable|string',
                'category_id'=>'required|exists:categories,id',
                'price'=>'required|numeric|min:0',
                    // sku opcional para servicios
                    'sku'=>'nullable|string|max:100|unique:products,sku',
            ]);
            if($validator->fails()){
                $this->errors[]=[
                    'row'=>$index+1,
                    'errors'=>$validator->errors()->all(),
                ];
                continue;
            }
            $data['type']='service';
                if(empty($data['sku'])) unset($data['sku']);
            Product::create($data);
            $this->importedCount++;
        }
    }

    public function getErrors(): array { return $this->errors; }
    public function getImportedCount(): int { return $this->importedCount; }
}
