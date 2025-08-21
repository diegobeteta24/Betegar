<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('movement.view', Movement::class);
      
        return view('admin.movements.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movements.create');
    }

    /**
     * PDF
     */
  public function pdf(Movement $movement)
    {
        $pdf = Pdf::loadView('admin.movements.pdf', [
            'model' => $movement,
        ]);

        return $pdf->download("movimiento_{$movement->id}.pdf");

    }
}
