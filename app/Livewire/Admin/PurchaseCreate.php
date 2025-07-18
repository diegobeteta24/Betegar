<?php

namespace App\Livewire\Admin;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Purchase;
use App\Facades\Kardex;
use Livewire\Component;

class PurchaseCreate extends Component
{
    public $voucher_type = 1;
    public $serie;
    public $correlative;
    public $date;
    public $purchase_order_id;
    public $supplier_id;
    public $warehouse_id;
    public $total = 0;
    public $observation;
    public $product_id;
    public $products = [];

    public function boot()
    {
        // Captura errores de validación para mostrarlos con SweetAlert
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $html = "<ul class='text-left'>";
                foreach ($validator->errors()->all() as $msg) {
                    $html .= "<li>{$msg}</li>";
                }
                $html .= "</ul>";
                $this->dispatch('swal', [
                    'title' => 'Errores de validación',
                    'html'  => $html,
                    'icon'  => 'error',
                ]);
            }
        });
    }

    public function updated($property, $value)
    {
        // Al cambiar la orden de compra, cargo datos y productos
        if ($property === 'purchase_order_id') {
            $po = PurchaseOrder::with('products')->find($value);
            if ($po) {
                $this->voucher_type  = $po->voucher_type;
                $this->supplier_id   = $po->supplier_id;
                // Si tu PurchaseOrder tiene warehouse_id, lo asignas; sino fuerza null
                $this->warehouse_id  = $po->warehouse_id ?? null;

                $this->products = $po->products->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price'    => $product->pivot->price,
                    ];
                })->toArray();
            }
        }
    }

    public function addProduct()
    {
        // Valido que exista producto y almacén
        $this->validate([
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ], [], [
            'product_id'   => 'Producto',
            'warehouse_id' => 'Almacén',
        ]);

        // Evito duplicados
        if (collect($this->products)->firstWhere('id', $this->product_id)) {
            $this->dispatch('swal', [
                'title' => 'Producto ya agregado',
                'text'  => 'El producto ya está en la lista.',
                'icon'  => 'warning',
            ]);
            return;
        }

        $product    = Product::find($this->product_id);
        $lastRecord = Kardex::getLastRecord($product->id, $this->warehouse_id);

        $this->products[] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'quantity' => 1,
            'price'    => $lastRecord['cost'],
        ];

        $this->reset('product_id');
    }

    public function save()
    {
        // Validación final
        $this->validate([
            'voucher_type'       => 'required|in:1,2',
            'serie'              => 'required|string|max:10',
            'correlative'        => 'required|string|max:10',
            'date'               => 'nullable|date',
            'purchase_order_id'  => 'nullable|exists:purchase_orders,id',
            'supplier_id'        => 'required|exists:suppliers,id',
            'warehouse_id'       => 'required|exists:warehouses,id',
            'total'              => 'required|numeric|min:0',
            'observation'        => 'nullable|string|max:255',
            'products'           => 'required|array|min:1',
            'products.*.id'      => 'required|exists:products,id',
            'products.*.quantity'=> 'required|numeric|min:1',
            'products.*.price'   => 'required|numeric|min:0',
        ], [], [
            'voucher_type'        => 'Tipo de Documento',
            'serie'               => 'Serie',
            'correlative'         => 'Correlativo',
            'date'                => 'Fecha',
            'supplier_id'         => 'Proveedor',
            'warehouse_id'        => 'Almacén',
            'total'               => 'Total',
            'observation'         => 'Observación',
            'products'            => 'Productos',
            'products.*.id'       => 'Producto',
            'products.*.quantity' => 'Cantidad',
            'products.*.price'    => 'Precio',
        ]);

        // Creo la compra
        $purchase = Purchase::create([
            'voucher_type'      => $this->voucher_type,
            'serie'             => $this->serie,
            'correlative'       => $this->correlative,
            'date'              => $this->date ?? now(),
            'purchase_order_id' => $this->purchase_order_id,
            'supplier_id'       => $this->supplier_id,
            'warehouse_id'      => $this->warehouse_id,
            'total'             => $this->total,
            'observation'       => $this->observation,
        ]);

        // Detalles y Kardex
        foreach ($this->products as $p) {
            $purchase->products()->attach($p['id'], [
                'quantity' => $p['quantity'],
                'price'    => $p['price'],
                'subtotal' => $p['quantity'] * $p['price'],
            ]);
            Kardex::registerEntry($purchase, $p, $this->warehouse_id, 'Compra de producto');
        }

        return redirect()
            ->route('admin.purchases.index')
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Compra creada!',
                'text'              => 'Ahora puedes editar los detalles.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    public function render()
    {
        return view('livewire.admin.purchase-create');
    }
}
