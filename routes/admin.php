
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
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
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalePaymentController;
use App\Http\Controllers\Admin\BankTransactionController;
use App\Http\Controllers\Admin\WorkOrderController as AdminWorkOrderController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ServiceController;

// Mapa de técnicos (solo admin)
Route::get('technicians/map', function() {
    return view('admin.technicians.map');
})->middleware('role:admin')->name('technicians.map');

// Overview (fondos / balances / gastos) de técnicos (solo admin)
Route::get('technicians/overview', function() {
    return view('admin.technicians.overview');
})->middleware('role:admin')->name('technicians.overview');

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');


Route::resource('categories', CategoryController::class)->except(['show']);

Route::resource('products', ProductController::class)->except(['show']);
// Servicios (comparten tabla products con type=service)
Route::resource('services', ServiceController::class)->except(['show']);

// Papelera (soft deletes) products
Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

Route::post('products/{product}/dropzone', [ProductController::class, 'dropzone'])
    ->name('products.dropzone');

Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])
    ->name('products.kardex');
    
Route::resource('customers', CustomerController::class)->except(['show']);


Route::resource('suppliers', SupplierController::class)->except(['show']);

Route::resource('warehouses', WarehouseController::class)->except(['show']);

Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('quotes', QuoteController ::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
Route::put('quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
Route::patch('quotes/{quote}', [QuoteController::class, 'update']);
Route::resource('sales', SaleController ::class)->only(['index', 'create', 'store', 'destroy']);
// Expenses (admin visualización de comprobantes)
Route::get('expenses', function(){ return view('admin.expenses.index'); })->name('expenses.index');
Route::get('expenses/{expense}', function(\App\Models\Expense $expense){ return view('admin.expenses.show', compact('expense')); })->name('expenses.show');

// Work Orders (admin-only section)
Route::get('work-orders', [AdminWorkOrderController::class, 'index'])->name('work-orders.index');
Route::get('work-orders/create', [AdminWorkOrderController::class, 'create'])->name('work-orders.create');
// Import must go BEFORE dynamic {workOrder} route to avoid collision with the "import" literal.
Route::get('work-orders/import', [AdminWorkOrderController::class, 'import'])->name('work-orders.import');
Route::post('work-orders', [AdminWorkOrderController::class, 'store'])->name('work-orders.store');
// Mostrar orden (detalle) – constrain to numeric to prevent catching /import
Route::get('work-orders/{workOrder}', [AdminWorkOrderController::class, 'show'])
    ->whereNumber('workOrder')
    ->name('work-orders.show');

//PDF con laravel-dompdf
Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
    ->name('purchases.pdf');
Route::get('purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])
    ->name('purchase-orders.pdf');
Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])
    ->name('quotes.pdf');
Route::get('quotes/{quote}/public', [QuoteController::class, 'public'])
    ->name('quotes.public');
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
Route::get('customers/import', [CustomerController::class, 'import'])
    ->name('customers.import');
Route::get('suppliers/import', [SupplierController::class, 'import'])
    ->name('suppliers.import');
Route::get('expense-categories/import', [ExpenseCategoryController::class, 'import'])
    ->name('expense-categories.import');
Route::get('bank-accounts/import', [BankAccountController::class, 'import'])
    ->name('bank-accounts.import');
Route::get('quotes/import', [QuoteController::class, 'import'])
    ->name('quotes.import');
Route::get('sales/import', [SaleController::class, 'import'])
    ->name('sales.import');
Route::get('purchases/import', [PurchaseController::class, 'import'])
    ->name('purchases.import');
Route::get('purchase-orders/import', [PurchaseOrderController::class, 'import'])
    ->name('purchase-orders.import');
Route::get('services/import', [ServiceController::class, 'import'])
    ->name('services.import');


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

Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

// ——————— Bank Accounts ———————
Route::resource('bank-accounts', BankAccountController::class)
     ->except(['show']);

// Bank Transactions
Route::get('bank-transactions', [BankTransactionController::class, 'index'])
    ->name('bank-transactions.index');
Route::get('bank-transactions/create', [BankTransactionController::class, 'create'])
    ->name('bank-transactions.create');
Route::post('bank-transactions', [BankTransactionController::class, 'store'])
    ->name('bank-transactions.store');

// Sale payments (create/store + PDF receipt)
Route::get('sales/{sale}/payments/create', [SalePaymentController::class, 'create'])
    ->name('sales.payments.create');
Route::post('sales/{sale}/payments', [SalePaymentController::class, 'store'])
    ->name('sales.payments.store');
Route::get('sales/payments/{payment}/pdf', [SalePaymentController::class, 'pdf'])
    ->name('sales.payments.pdf');

// Sale payments index (datatable)
Route::get('sales/payments', function () {
    return view('admin.sales.payments.index');
})->name('sales.payments.index');

// Routes for Users, Roles, Permissions and Settings

Route::resource('users', UserController::class);
    

Route::resource('roles', RoleController::class);

// PermissionController inexistente
// Route::resource('permissions', PermissionController::class)->except(['show']);
Route::get('permissions', function(){
    return response('Permissions pendiente', 200);
})->name('permissions.index');

// SettingController inexistente
// Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
// Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
Route::get('settings', function(){
    return response('Settings pendiente', 200);
})->name('settings.index');

// CRM - Reminders MVP
Route::get('crm/reminders', function(){
    return view('admin.crm.reminders');
})->name('crm.reminders.index');

// Test push notification (admin only)
Route::get('push/test', function(){
    $user = auth()->user();
    if(!$user || !$user->hasRole('admin')) abort(403);
    $user->notify(new \App\Notifications\TechnicianEvent('Prueba Push','Funciona la notificación push', ['type'=>'test']));
    return response()->json(['ok'=>true]);
})->name('push.test');