<?php
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\MovementController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\ReportController;

Route::get('/', function () {
   return view('admin.dashboard');
  
})->name('dashboard');


Route::resource('categories', CategoryController::class)->except(['show']);

Route::resource('products', ProductController::class)->except(['show']);

Route::post('products/{product}/dropzone', [ProductController::class, 'dropzone'])
    ->name('products.dropzone');

Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])
    ->name('products.kardex');
    
Route::resource('customers', CustomerController::class)->except(['show']);


Route::resource('suppliers', SupplierController::class)->except(['show']);

Route::resource('warehouses', WarehouseController::class)->except(['show']);

Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('quotes', QuoteController ::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('sales', SaleController ::class)->only(['index', 'create', 'store', 'destroy']);

//PDF con laravel-dompdf
Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
    ->name('purchases.pdf');
Route::get('purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])
    ->name('purchase-orders.pdf');
Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])
    ->name('quotes.pdf');
Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])
    ->name('sales.pdf');
Route::get('transfers/{transfer}/pdf', [TransferController::class, 'pdf'])
    ->name('transfers.pdf');
Route::get('movements/{movement}/pdf', [MovementController::class, 'pdf'])
    ->name('movements.pdf');

// Rutas para importar con laravel-excel
Route::get('products/import', [ProductController::class, 'import'])
    ->name('products.import');
Route::get('categories/import', [CategoryController::class, 'import'])
    ->name('categories.import');
Route::get('warehouses/import', [WarehouseController::class, 'import'])
    ->name('warehouses.import');


// Borrar imágenes
Route::delete('images/{image}', [ImageController::class, 'destroy'])
    ->name('images.destroy');

//Routes for MovementController
Route::resource('movements', MovementController::class)
    ->only(['index', 'create', 'store', 'destroy']);

//Routes for TransferController
Route::resource('transfers', TransferController::class)
    ->only(['index', 'create', 'store', 'destroy']);

//Rutas para reportes
Route::get('reports/top-products', [ReportController::class, 'topProducts'])
    ->name('reports.top-products');
Route::get('reports/top-customers', [ReportController::class, 'topCustomers'])
    ->name('reports.top-customers');
Route::get('reports/low-stock', [ReportController::class, 'lowStock'])
    ->name('reports.low-stock');