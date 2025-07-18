<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transfer;


class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        return view('admin.transfers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.transfers.create');
    }

    /**
     * PDF
     */
   public function pdf(Transfer $transfer)
    {
        $pdf = Pdf::loadView('admin.transfers.pdf', [
            'model' => $transfer,
        ]);

        return $pdf->download("transferencia_{$transfer->id}.pdf");

    }
}
