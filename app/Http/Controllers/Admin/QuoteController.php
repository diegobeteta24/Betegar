<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Quote;
use Illuminate\Support\Str;


class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        return view('admin.quotes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.quotes.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        return view('admin.quotes.edit', compact('quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        // La lógica de actualización vive principalmente en el componente Livewire QuoteEdit.
        // Este método se mantiene para cumplir con la ruta RESTful si se necesitara un guardado tradicional.
        return redirect()->route('admin.quotes.edit', $quote);
    }

    /**
     * PDF
     */
  public function pdf(Quote $quote)
    {
        $pdf = Pdf::loadView('admin.quotes.pdf', [
            'model' => $quote,
        ]);

        return $pdf->download("cotizacion_{$quote->id}.pdf");

    }

    /**
     * Ensure public token and redirect to public view.
     */
    public function public(Quote $quote)
    {
        if(!$quote->public_token){
            $quote->public_token = (string) Str::uuid();
            $quote->save();
        }
        return redirect()->route('public.quote.show', $quote->public_token);
    }

    /**
     * Formulario importación masiva de cotizaciones (solo encabezados).
     */
    public function import(Request $request)
    {
    $this->authorize('quote.import');
    return view('admin.quotes.import');
    }
}
