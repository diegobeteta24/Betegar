<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Quote;
use App\Models\Product;

class QuoteEdit extends Component
{
    public Quote $quote;
    public $voucher_type;
    public $serie;
    public $correlative;
    public $date;
    public $customer_id;
    public $customer_address_id;
    public $customer_addresses = [];
    public $products = [];
    public $subtotal = 0;
    public $discount_percent = 0;
    public $discount_amount = 0;
    public $total = 0;
    public $observation;
    public $product_id; // for adding new product

    public function mount(Quote $quote)
    {
        $this->quote = $quote->load(['products', 'customer']);
        $this->voucher_type = $quote->voucher_type;
        $this->serie = $quote->serie;
        $this->correlative = $quote->correlative;
        $this->date = optional($quote->date)->format('Y-m-d');
        $this->customer_id = $quote->customer_id;
        $this->customer_address_id = $quote->customer_address_id;
        $this->discount_percent = $quote->discount_percent;
        $this->discount_amount = $quote->discount_amount;
        $this->subtotal = $quote->subtotal;
        $this->total = $quote->total;
        $this->observation = $quote->observation;

        // cargar direcciones
        $this->updatedCustomerId($this->customer_id);

        // map products with description
        $this->products = $quote->products->map(function($p){
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->pivot->description ?: $p->name,
                'quantity' => $p->pivot->quantity,
                'price' => $p->pivot->price,
                'subtotal' => $p->pivot->subtotal,
                'is_service' => $p->type === 'service',
            ];
        })->toArray();
    }

    public function addProduct()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id'
        ],[],['product_id' => 'Producto']);

        if(collect($this->products)->firstWhere('id',$this->product_id)){
            $this->dispatch('swal', [
                'title' => 'Producto ya agregado',
                'icon' => 'warning'
            ]);
            return;
        }
        $p = Product::find($this->product_id);
        $this->products[] = [
            'id'=>$p->id,
            'name'=>$p->name,
            'description'=>$p->name,
            'quantity'=>1,
            'price'=>$p->price,
            'subtotal'=>$p->price,
            'is_service'=>$p->type==='service'
        ];
        $this->reset('product_id');
        $this->recalculateTotals();
    }

    public function updatedProducts(){ $this->recalculateTotals(); }
    public function updatedDiscountPercent(){ $this->recalculateTotals(); }

    protected function recalculateTotals(){
        $this->subtotal = collect($this->products)->sum(fn($p)=> (float)$p['quantity'] * (float)$p['price']);
        $this->discount_amount = round($this->subtotal * ($this->discount_percent/100), 2);
        $this->total = max($this->subtotal - $this->discount_amount, 0);
    }

    public function updatedCustomerId($value)
    {
        $this->customer_address_id = null;
        $this->customer_addresses = [];
        if($value){
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
                })->toArray();
            $primary = collect($this->customer_addresses)->firstWhere('is_primary', true);
            if($primary) $this->customer_address_id = $primary['id'];
        }
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => 'required|in:1,2',
            'customer_id' => 'required|exists:customers,id',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ],[],[
            'voucher_type'=>'Tipo de Documento',
            'customer_id'=>'Cliente',
            'customer_address_id'=>'Dirección',
            'products'=>'Productos'
        ]);

        $this->recalculateTotals();

        $this->quote->update([
            'voucher_type'=>$this->voucher_type,
            'date'=>$this->date ?: now(),
            'customer_id'=>$this->customer_id,
            'customer_address_id'=>$this->customer_address_id,
            'subtotal'=>$this->subtotal,
            'discount_percent'=>$this->discount_percent,
            'discount_amount'=>$this->discount_amount,
            'total'=>$this->total,
            'observation'=>$this->observation,
        ]);

        // sync products pivot
        $sync = [];
        foreach($this->products as $p){
            $sync[$p['id']] = [
                'quantity'=>$p['quantity'],
                'price'=>$p['price'],
                'subtotal'=>$p['quantity']*$p['price'],
                'description'=>$p['description'] ?? null,
            ];
        }
        $this->quote->products()->sync($sync);

        return redirect()->route('admin.quotes.index')->with('sweet-alert', [
            'icon'=>'success',
            'title'=>'¡Cotización actualizada!',
            'timer'=>2500,
            'showConfirmButton'=>false
        ]);
    }

    public function render()
    {
        return view('livewire.admin.quote-edit');
    }
}
