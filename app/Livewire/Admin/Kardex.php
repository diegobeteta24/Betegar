<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use Livewire\WithPagination;

class Kardex extends Component
{
    use WithPagination;
    public Product $product;
    public $warehouses;
    public $warehouse_id;
    public $fecha_inicial;
    public $fecha_final;


    public function mount()
    {
        $this->warehouses = Warehouse::all();
        $this->warehouse_id = $this->warehouses->first()->id ?? null;
    }
        
    

    public function render()
    {
        $inventories = Inventory::where('product_id', $this->product->id)
       ->where('warehouse_id', $this->warehouse_id)
        ->when($this->fecha_inicial, function ($query) {
            $query->where('created_at', '>=', $this->fecha_inicial);
        })
        ->when($this->fecha_final, function ($query) {
            $query->where('created_at', '<=', $this->fecha_final);
        })
        ->orderBy('created_at', 'desc')
       ->paginate();

        return view('livewire.admin.kardex', compact('inventories'));
    }
}
