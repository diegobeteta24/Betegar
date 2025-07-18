<?php

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Reason;


Route::post('/warehouses', function(Request $request) {
    return Warehouse::select('id','name','location as description')
        // 1) Excluir el origen primero
        ->when($request->exclude, fn($q,$e) => $q->where('id','!=',$e))

        // 2) Agrupar el search en su propio WHERE (...)
        ->when($request->search, function($q,$s){
            $q->where(function($q2) use($s){
                $q2->where('name','like',   "%{$s}%")
                   ->orWhere('location','like', "%{$s}%");
            });
        })

        // 3) Devolver los seleccionados o los primeros 10
        ->when(
            $request->filled('selected'),
            fn($q) => $q->whereIn('id', (array) $request->input('selected')),
            fn($q) => $q->limit(10)
        )

        ->get();
})->name('api.warehouses.index');



Route::post('/suppliers', function(Request $request) {
    return Supplier::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('document_number','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get();
})->name('api.suppliers.index');

Route::post('/customers', function(Request $request) {
    return Customer::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('document_number','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get();
})->name('api.customers.index');

Route::post('/products', function(Request $request) {

    return Product::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('sku','like',"%{$search}%");
              
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get();
})->name('api.products.index');

Route::post('purchase-orders', function(Request $request) {

    $purchaseOrders = PurchaseOrder::when($request->search, function($query,$search) {

        $parts = explode('-', $search);

        if(count($parts) == 1) {

            $query->whereHas('supplier', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%");
            });
            return;
        }

        if (count($parts) == 2) {

        $serie = $parts[0];
        $correlative = ltrim($parts[1], '0');

        $query->where('serie',$serie)
              ->where('correlative', 'LIKE', "%{$correlative}%");
               return;  
        }

    })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->with(['supplier'])
        ->orderBy('created_at', 'desc')
        ->get();

        return $purchaseOrders->map(function($purchaseOrder) {

            return [
                'id' => $purchaseOrder->id,
                'name' => $purchaseOrder->serie . '-' . $purchaseOrder->correlative,
                'description' => $purchaseOrder->supplier->name . ' - ' . $purchaseOrder->supplier->document_number,

            ];
        });
           

   
})->name('api.purchase-orders.index');

Route::post('quotes', function(Request $request) {

    $quotes = Quote::when($request->search, function($query,$search) {

        $parts = explode('-', $search);

        if(count($parts) == 1) {

            $query->whereHas('customer', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%");
            });
            return;
        }

        if (count($parts) == 2) {

        $serie = $parts[0];
        $correlative = ltrim($parts[1], '0');

        $query->where('serie',$serie)
              ->where('correlative', 'LIKE', "%{$correlative}%");
               return;  
        }

    })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->with(['customer'])
        ->orderBy('created_at', 'desc')
        ->get();

        return $quotes->map(function($quote) {

            return [
                'id' => $quote->id,
                'name' => $quote->serie . '-' . $quote->correlative,
                'description' => $quote->customer->name . ' - ' . $quote->customer->document_number,

            ];
        });
           

   
})->name('api.sales.index');

//Reason api
Route::post('/reasons', function(Request $request) {
    return Reason::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->where('type', $request->input('type', '')) // Default to type 1 if not provided
        ->get();
})->name('api.reasons.index');


