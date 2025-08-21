<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Purchase;



class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        return view('admin.purchase_orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.purchase_orders.create');
    }

    /**
     * Formulario importación masiva de órdenes de compra.
     */
    public function import(Request $request)
    {
        $this->authorize('purchase-order.import');
        return view('admin.purchase_orders.import');
    }

    /**
     * PDF generation for purchase orders.
     */
   public function pdf(PurchaseOrder $purchaseOrder)
    {
        $pdf = Pdf::loadView('admin.purchase_orders.pdf', [
            'model' => $purchaseOrder,
        ]);

        return $pdf->download("orden_de_compra_{$purchaseOrder->id}.pdf");

    }
    
}
