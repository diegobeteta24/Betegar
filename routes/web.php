<?php

// (REMOVED) Comentarios y rutas públicas que producían salida antes de la etiqueta PHP, rompiendo respuestas JSON de Livewire.

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route as WebRoute;
use App\Http\Controllers\PublicQuoteController;

//Redirigir a dashboard operativo (no admin)
Route::get('/', function () {
   return view('dashboard');
  
})->name('dashboard');

// Cotización pública individual
Route::get('/cotizacion/{token}', [PublicQuoteController::class, 'show'])->name('public.quote.show');

// Work Orders pages (protected by web+auth via bootstrap routing)
Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'showPage'])
   ->name('work-orders.show');

// Página para registrar gastos (técnicos)
WebRoute::middleware(['web','auth'])->group(function(){
   WebRoute::view('/expenses/create', 'expenses.create')->name('expenses.create');
});

