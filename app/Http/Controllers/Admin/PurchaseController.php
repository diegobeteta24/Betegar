<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.purchases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.purchases.create');
    }

    /**
     * Formulario importación masiva de compras.
     */
    public function import(Request $request)
    {
        $this->authorize('purchase.import');
        return view('admin.purchases.import');
    }

    /**
     * PDF generation for purchases.
     */

    public function pdf(Purchase $purchase)
    {
        $pdf = Pdf::loadView('admin.purchases.pdf', [
            'model' => $purchase,
        ]);

        return $pdf->download("compra_{$purchase->id}.pdf");        
    
    }
}
