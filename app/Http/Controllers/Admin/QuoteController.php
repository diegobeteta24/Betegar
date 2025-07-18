<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Quote;


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
     * PDF
     */
  public function pdf(Quote $quote)
    {
        $pdf = Pdf::loadView('admin.quotes.pdf', [
            'model' => $quote,
        ]);

        return $pdf->download("cotizacion_{$quote->id}.pdf");

    }
}
