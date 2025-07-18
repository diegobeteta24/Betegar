<?php

namespace App\Livewire\Admin;
use App\Models\PurchaseOrder;
use App\Models\Product;

use Livewire\Component;

class PurchaseOrderCreate extends Component
{

public $voucher_type=1;
public $serie='OC01';
public $correlative;
public $date;
public $supplier_id;
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
    $this->correlative = PurchaseOrder::max('correlative') + 1;
   
}

public function addProduct()
{
    $this->validate([
        'product_id' => 'required|exists:products,id',
    ],[],[
        'product_id' => 'Producto',
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
    
    
   
        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 1, // Default quantity
            'price' => 0,
            'subtotal' => 0,
            
        ];

        $this->reset('product_id'); // Reset product selection

}

public function save()
{
    $this->validate([
        'voucher_type' => 'required|in:1,2', // Assuming 1 for OC and 2 for other types
        // 'serie' => 'required',
        // 'correlative' => 'required|unique:purchase_orders,correlative',
        'date' => 'nullable|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'total' => 'required|numeric|min:0',
        'observation' => 'nullable|string|max:255',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    
    ], [], [
        'voucher_type' => 'Tipo de Documento',
        'serie' => 'Serie',
        'correlative' => 'Correlativo',
        'date' => 'Fecha',
        'supplier_id' => 'Proveedor',
        'total' => 'Total',
        'observation' => 'Observación',
        'products' => 'Productos',
        'products.*.id' => 'Producto',
        'products.*.quantity' => 'Cantidad',
        'products.*.price' => 'Precio',
    ]);

    $purchaseOrder = PurchaseOrder::create([
        'voucher_type' => $this->voucher_type,
        'serie' => $this->serie,
        'correlative' => $this->correlative,
        'date' => $this->date ?? now(),
        'supplier_id' => $this->supplier_id,
        'total' => $this->total,
        'observation' => $this->observation,
    ]);

    foreach ($this->products as $product) {
        $purchaseOrder->products()->attach($product['id'], [
            'quantity' => $product['quantity'],
            'price' => $product['price'],
            'subtotal' => $product['quantity'] * $product['price'],
        ]);
    }

    return redirect()
            ->route('admin.purchase-orders.index')
             ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Orden de compra creada!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,   // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);

}
    public function render()
    {
        return view('livewire.admin.purchase-order-create');
    }

}
