<?php

use Illuminate\Support\Facades\Route;
use App\Models\Productable;

       
Route::redirect('/', '/admin');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/prueba', function () {
   return Productable::where('productable_type', 'App\Models\Sale')
   ->join('products', 'productables.product_id', '=', 'products.id')
   ->selectRaw('
       products.id as product_id,
       products.name as name,
       products.description as description,
       SUM(productables.quantity) as quantity,
       SUM(productables.subtotal) as subtotal
   ')
    ->groupBy('products.id', 'products.name', 'products.description')
    ->orderBy('subtotal', 'desc')
   ->get();
   
})->name('prueba');
