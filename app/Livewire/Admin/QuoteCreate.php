<?php

namespace App\Livewire\Admin;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Str;

use Livewire\Component;

class QuoteCreate extends Component
{

public $voucher_type=1;
public $serie='C001';
public $correlative;
public $date;
public $customer_id;
public $customer_address_id; // nueva selección de dirección
public $customer_addresses = []; // opciones cargadas
public $total = 0;
public $observation;
public $product_id;
public $products = [];
public $discount_percent = 0; // porcentaje aplicado sobre subtotal
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
    $this->correlative = Quote::max('correlative') + 1;
   
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
            'description' => $product->name, // editable descripción inicial
            'quantity' => 1, // Default quantity
            'price' => $product->price, // Default price
            'subtotal' => $product->price, // Default subtotal
            'is_service' => $product->type === 'service',
        ];

    $this->reset('product_id'); // Reset product selection
    $this->recalculateTotals();

}

public function save()
{
    $this->validate([
        'voucher_type' => 'required|in:1,2', // Assuming 1 for OC and 2 for other types
        // 'serie' => 'required',
        // 'correlative' => 'required|unique:purchase_orders,correlative',
        'date' => 'nullable|date',
    'customer_id' => 'required|exists:customers,id',
    'customer_address_id' => 'nullable|exists:customer_addresses,id',
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
    'customer_id' => 'Cliente',
    'customer_address_id' => 'Dirección',
        'total' => 'Total',
        'observation' => 'Observación',
        'products' => 'Productos',
        'products.*.id' => 'Producto',
        'products.*.quantity' => 'Cantidad',
        'products.*.price' => 'Precio',
    ]);

    $this->recalculateTotals();

    $quote = Quote::create([
        'voucher_type' => $this->voucher_type,
        'serie' => $this->serie,
        'correlative' => $this->correlative,
        'date' => $this->date ?? now(),
        'customer_id' => $this->customer_id,
        'customer_address_id' => $this->customer_address_id,
        'subtotal' => $this->subtotal,
        'discount_percent' => $this->discount_percent,
        'discount_amount' => $this->discount_amount,
        'total' => $this->total,
        'observation' => $this->observation,
        'public_token' => (string) \Str::uuid(),
    ]);

    foreach ($this->products as $product) {
        $quote->products()->attach($product['id'], [
            'quantity' => $product['quantity'],
            'price' => $product['price'],
            'subtotal' => $product['quantity'] * $product['price'],
            'description' => $product['description'] ?? null,
        ]);
    }

    return redirect()
            ->route('admin.quotes.index')
             ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Cotización creada!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,   // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);

}
public function updatedDiscountPercent()
{
    $this->recalculateTotals();
}

public function updatedProducts()
{
    $this->recalculateTotals();
}

protected function recalculateTotals(): void
{
    $this->subtotal = collect($this->products)->sum(fn($p)=> (float)$p['quantity'] * (float)$p['price']);
    $this->discount_amount = round($this->subtotal * ($this->discount_percent/100), 2);
    $this->total = max($this->subtotal - $this->discount_amount, 0);
}
    public function updatedCustomerId($value)
    {
        // Reset address selection when customer changes
        $this->customer_address_id = null;
        $this->customer_addresses = [];
        if($value) {
            $this->customer_addresses = \App\Models\CustomerAddress::where('customer_id',$value)
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->get(['id','label','address','is_primary'])
                ->map(function($a){
                    return [
                        'id'=>$a->id,
                        'label'=>$a->label ?: 'Dirección',
                        'address'=>$a->address,
                        'is_primary'=>$a->is_primary,
                        'text'=> ($a->label ? ($a->label.' - ') : '').$a->address.($a->is_primary ? ' (Principal)' : ''),
                    ];
                })
                ->toArray();
            // Auto seleccionar primaria
            $primary = collect($this->customer_addresses)->firstWhere('is_primary', true);
            if($primary) { $this->customer_address_id = $primary['id']; }
        }
    }
    public function render()
    {
        return view('livewire.admin.quote-create');
    }

}
