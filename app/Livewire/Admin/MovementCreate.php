<?php

namespace App\Livewire\Admin;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Movement;
use App\Models\Inventory;
use App\Services\KardexService;
use App\Facades\Kardex;

use Livewire\Component;

class MovementCreate extends Component
{

    //Properties for the form

public $type=1;
public $serie='M001';
public $correlative;
public $date;
public $warehouse_id;
public $reason_id;
public $total = 0;
public $observation;
public $product_id;
public $products = [];

public function boot()
{
    $this->withValidator(function ($validator) {
      if ($validator->fails()) {

        $errors = $validator->errors()->toArray();

        $html= "<ul class='text-left'>";

        foreach ($errors as $error) {
            $html .= "<li>{$error[0]}</li>";
        }

        $html .= "</ul>";

        $this->dispatch('swal', [
            'title' => 'Errores de validación',
            'html' => $html,
            'icon' => 'error',
        ]);
    }
        });
    }

    public function updated($property, $value)
    {
        if ($property =='type') {
            $this->reset('reason_id');
        }
    }
    




public function mount()
{
    $this->correlative = Movement::max('correlative') + 1;
   
}

public function addProduct()
{
    $this->validate([
        'product_id' => 'required|exists:products,id',
        'warehouse_id' => 'required|exists:warehouses,id',
    ],[],[
        'product_id' => 'Producto',
        'warehouse_id' => 'Almacén',
    ]);

    $existing= collect($this->products)
    ->firstWhere('id', $this->product_id);

    if($existing) {
       
        $this->dispatch('swal', [
            'title' => 'Producto ya agregado',
            'text' => 'El producto ya está en la lista.',
            'icon' => 'warning',
        ]);

        return;
        
    }

    $product = Product::find($this->product_id);
    $lastRecord= Inventory::where('product_id', $product->id)
            ->where('warehouse_id', $this->warehouse_id)
            ->latest('id')
            ->first();
           

    $costBalance = $lastRecord?->cost_balance ?? 0;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1, // Default quantity
            'price' => $costBalance,
            'subtotal' => $costBalance, // Default subtotal
        ];

        $this->reset('product_id'); // Reset product selection

}

public function save()
{
    $this->validate([
        'type' => 'required|in:1,2', // Assuming 1 for OC and 2 for other types
        'serie' => 'required|string|max:10',
        'correlative' => 'required|numeric|min:1',
        'date' => 'nullable|date',
        'warehouse_id' => 'required|exists:warehouses,id',
        'reason_id' => 'required|exists:reasons,id',
        'total' => 'required|numeric|min:0',
        'observation' => 'nullable|string|max:255',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    
    ], [], [
        'type' => 'Tipo de Movimiento',
        'serie' => 'Serie',
        'correlative' => 'Correlativo',
        'date' => 'Fecha',
        'warehouse_id' => 'Almacén',
        'total' => 'Total',
        'observation' => 'Observación',
        'products' => 'Productos',
        'products.*.id' => 'Producto',
        'products.*.quantity' => 'Cantidad',
        'products.*.price' => 'Precio',
    ]);

    $movement = Movement::create([
        'type' => $this->type,
        'serie' => $this->serie,
        'correlative' => $this->correlative,
        'date' => $this->date ?? now(),
        'warehouse_id' => $this->warehouse_id,
        'total' => $this->total,
        'observation' => $this->observation,
        'reason_id' => $this->reason_id,
    ]);

    foreach ($this->products as $product) {
        $movement->products()->attach($product['id'], [
            'quantity' => $product['quantity'],
            'price' => $product['price'],
            'subtotal' => $product['quantity'] * $product['price'],
        ]);



if ($this->type == 1) {
            // Register entry in Kardex
            Kardex::registerEntry($movement, $product, $this->warehouse_id, 'Movimiento de entrada');
        } elseif ($this->type == 2) {
            // Register exit in Kardex
            Kardex::registerExit($movement, $product, $this->warehouse_id, 'Movimiento de salida');
        }
    }

    

    return redirect()
            ->route('admin.movements.index')
            ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Movimiento creado!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,   // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);

}
    public function render()
    {
        return view('livewire.admin.movement-create');
    }

}
