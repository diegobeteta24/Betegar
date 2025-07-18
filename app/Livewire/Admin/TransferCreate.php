<?php

namespace App\Livewire\Admin;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\Inventory;
use App\Services\KardexService;
use App\Facades\Kardex;

use Livewire\Component;

class TransferCreate extends Component
{

    //Properties for the form


public $serie='T001';
public $correlative;
public $date;
public $origin_warehouse_id;
public $destination_warehouse_id;
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


public function mount()
{
    $this->correlative = Transfer::max('correlative') + 1;
   
}

public function updated($property, $value)
{
    if ($property =='origin_warehouse_id') {
       
        $this->reset('destination_warehouse_id');

    }
}


public function addProduct()
{
    $this->validate([
        'product_id' => 'required|exists:products,id',
        'origin_warehouse_id' => 'required|exists:warehouses,id',
        'destination_warehouse_id' => 'required|exists:warehouses,id|different:origin_warehouse_id',
    ],[],[
        'product_id' => 'Producto',
        'origin_warehouse_id' => 'Almacén de origen',
        'destination_warehouse_id' => 'Almacén de destino',
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
    $lastRecord = Kardex::getLastRecord($product->id, $this->origin_warehouse_id);
    
   
        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1, // Default quantity
           'price' => $lastRecord['cost'], // Use the last cost balance
            'subtotal' => $lastRecord['cost'], // Default subtotal
        ];

        $this->reset('product_id'); // Reset product selection

}

public function save()
{
    $this->validate([
        'serie' => 'required|string|max:10',
        'correlative' => 'required|numeric|min:1',
        'date' => 'nullable|date',
        'origin_warehouse_id' => 'required|exists:warehouses,id',
        //Destiny has to be different from origin
        'destination_warehouse_id' => 'required|exists:warehouses,id|different:origin_warehouse_id',
        'total' => 'required|numeric|min:0',
        'observation' => 'nullable|string|max:255',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    
    ], [], [
        
        'serie' => 'Serie',
        'correlative' => 'Correlativo',
        'date' => 'Fecha',
        'origin_warehouse_id' => 'Almacén de origen',
        'destination_warehouse_id' => 'Almacén de destino',
        'total' => 'Total',
        'observation' => 'Observación',
        'products' => 'Productos',
        'products.*.id' => 'Producto',
        'products.*.quantity' => 'Cantidad',
        'products.*.price' => 'Precio',
    ]);

    $transfer = Transfer::create([
        'serie' => $this->serie,
        'correlative' => $this->correlative,
        'date' => $this->date ?? now(),
        'origin_warehouse_id' => $this->origin_warehouse_id,
        'destination_warehouse_id' => $this->destination_warehouse_id,
        'total' => $this->total,
        'observation' => $this->observation,
    ]);

    foreach ($this->products as $product) {
        $transfer->products()->attach($product['id'], [
            'quantity' => $product['quantity'],
            'price' => $product['price'],
            'subtotal' => $product['quantity'] * $product['price'],
        ]);

        // Register the exit in the Kardex
        Kardex::registerExit($transfer, $product, $this->origin_warehouse_id, 'Salida por transferencia');
        // Register the entry in the Kardex
        Kardex::registerEntry($transfer, $product, $this->destination_warehouse_id, 'Entrada por transferencia');
    }

    return redirect()
            ->route('admin.transfers.index')
            ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Transferencia creada!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,   // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);

}
    public function render()
    {
        return view('livewire.admin.transfer-create');
    }

}
