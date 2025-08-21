<?php

namespace App\Livewire\Admin;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Quote;
use App\Models\Inventory;
use App\Services\KardexService;
use App\Facades\Kardex;


use Livewire\Component;

class SaleCreate extends Component
{

public $voucher_type=1;
public $serie="F001";
public $correlative;
public $date;
public $quote_id;
public $customer_id;
public $warehouse_id;
public $total = 0;
public $observation;
public $product_id;
public $products = [];
public $discount_percent = 0;
public $subtotal = 0;
public $discount_amount = 0;

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
    $this->correlative = Quote::max('correlative') + 1; // Increment the last correlative
}






public function updated($property, $value)
{
    if ($property === 'quote_id') {
        $quote = Quote::with('products')->find($value);
        if ($quote) {
            $this->voucher_type = $quote->voucher_type;
            $this->customer_id = $quote->customer_id;
            $this->discount_percent = $quote->discount_percent ?? 0;
            $this->discount_amount = $quote->discount_amount ?? 0;
            $this->subtotal = $quote->subtotal ?? 0;
            $this->total = $quote->total ?? 0;
            // map including type & description for service detection
            $this->products = $quote->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'subtotal' => $product->pivot->quantity * $product->pivot->price,
                    'is_service' => $product->type === 'service',
                    'description' => $product->pivot->description ?? null,
                ];
            })->toArray();
        }
    }
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
            'price' => $product->price,
            'subtotal' => $product->price, // Default subtotal
            'is_service' => $product->type === 'service',
            'description' => $product->name,
        ];

        $this->reset('product_id'); // Reset product selection
    $this->recalculateTotals();

}

public function save()
{
    // detect if there are any products that are not services
    $hasPhysical = collect($this->products)->contains(fn($p)=> empty($p['is_service']));

    $rules = [
        'voucher_type' => 'required|in:1,2',
        'serie' => 'required|string|max:10',
        'correlative' => 'required|numeric',
        'date' => 'nullable|date',
        'quote_id' => 'nullable|exists:quotes,id',
        'customer_id' => 'required|exists:customers,id',
        'total' => 'required|numeric|min:0',
        'observation' => 'nullable|string|max:255',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:1',
        'products.*.price' => 'required|numeric|min:0',
    ];
    if($hasPhysical){
        $rules['warehouse_id'] = 'required|exists:warehouses,id';
    } else {
        $rules['warehouse_id'] = 'nullable';
    }
    $this->validate($rules, [], [
        'voucher_type' => 'Tipo de Documento',
        'serie' => 'Serie',
        'correlative' => 'Correlativo',
        'date' => 'Fecha',
        'customer_id' => 'Cliente',
        'warehouse_id' => 'Almacén',
        'total' => 'Total',
        'observation' => 'Observación',
        'products' => 'Productos',
        'products.*.id' => 'Producto',
        'products.*.quantity' => 'Cantidad',
        'products.*.price' => 'Precio',
    ]);

    $this->recalculateTotals();

    if(!$hasPhysical){
        $this->warehouse_id = null; // ensure null persists
    }

    $sale = Sale::create([
        'voucher_type' => $this->voucher_type,
        'serie' => $this->serie,
        'correlative' => $this->correlative,
        'date' => $this->date ?? now(),
        'quote_id' => $this->quote_id,
        'customer_id' => $this->customer_id,
        'warehouse_id' => $this->warehouse_id,
        'subtotal' => $this->subtotal,
        'discount_percent' => $this->discount_percent,
        'discount_amount' => $this->discount_amount,
        'total' => $this->total,
        'observation' => $this->observation,
    ]);

    foreach ($this->products as $product) {
        $sale->products()->attach($product['id'], [
            'quantity' => $product['quantity'],
            'price' => $product['price'],
            'subtotal' => $product['quantity'] * $product['price'],
            'description' => $product['description'] ?? null,
        ]);
        // Kardex only for physical products
        if(empty($product['is_service']) && $this->warehouse_id){
            Kardex::registerExit($sale, $product, $this->warehouse_id, 'Venta de producto');
        }
    }

    return redirect()
            ->route('admin.sales.index')
             ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Venta creada!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,   // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);

}
public function updatedDiscountPercent(){ $this->recalculateTotals(); }
public function updatedProducts(){ $this->recalculateTotals(); }
protected function recalculateTotals(): void
{
    $this->subtotal = collect($this->products)->sum(fn($p)=> (float)$p['quantity'] * (float)$p['price']);
    $this->discount_amount = round($this->subtotal * ($this->discount_percent/100), 2);
    $this->total = max($this->subtotal - $this->discount_amount, 0);
}
    public function render()
    {
        return view('livewire.admin.sale-create');
    }

}
