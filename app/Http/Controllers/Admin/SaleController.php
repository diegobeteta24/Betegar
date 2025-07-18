<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Sale; // Assuming Sale model exists

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.sales.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
  public function pdf(Sale $sale)
    {
        $pdf = Pdf::loadView('admin.sales.pdf', [
            'model' => $sale,
        ]);

        return $pdf->download("venta_{$sale->id}.pdf");

    }
}
