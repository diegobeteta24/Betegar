<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class SalePaymentController extends Controller
{
    /**
     * Show payment form for a sale.
     */
    public function create(Sale $sale)
    {
        $accounts = BankAccount::orderBy('name')->get(['id','name','currency']);
        return view('admin.sales.payments.create', compact('sale', 'accounts'));
    }

    /**
     * Store a new payment for the sale and create the bank transaction.
     */
    public function store(Request $request, Sale $sale)
    {
        // Prevent new payment if already fully paid
        if ($sale->due_amount <= 0.00001) {
            return back()->withErrors(['amount' => 'La venta ya está completamente pagada.']);
        }
        $max = (float) $sale->due_amount;

        $validated = $request->validate([
            'bank_account_id' => ['required', Rule::exists('bank_accounts', 'id')],
            'amount'          => ['required', 'numeric', 'min:0.01', 'max:' . max($max, 0.01)],
            'method'          => ['required', 'string', 'max:50'],
            'reference'       => ['nullable', 'string', 'max:100'],
            'paid_at'         => ['required', 'date'],
        ], [], [
            'bank_account_id' => 'Cuenta bancaria',
            'amount'          => 'Monto',
            'method'          => 'Método',
            'reference'       => 'Referencia',
            'paid_at'         => 'Fecha de pago',
        ]);

    $payment = DB::transaction(function () use ($sale, $validated) {
            /** @var SalePayment $payment */
            $payment = $sale->payments()->create([
                'bank_account_id' => $validated['bank_account_id'],
                'amount'          => $validated['amount'],
                'method'          => $validated['method'],
                'reference'       => $validated['reference'] ?? null,
                'paid_at'         => $validated['paid_at'],
            ]);

            // Create bank transaction (credit)
            $desc = sprintf(
                'Pago Venta #%s-%s (%s%s)%s',
                $sale->serie ?? '—',
                str_pad((string)($sale->correlative ?? $sale->id), 4, '0', STR_PAD_LEFT),
                strtoupper($validated['method']),
                $validated['reference'] ? ':' . $validated['reference'] : '',
                ''
            );

            $payment->transaction()->create([
                'bank_account_id' => $validated['bank_account_id'],
                'type'            => 'credit',
                'date'            => $validated['paid_at'],
                'amount'          => $validated['amount'],
                'description'     => $desc,
            ]);

            // Refresh sale to update paid/due derived attributes
            $sale->refresh();
            return $payment;
        });

        return redirect()
            ->route('admin.sales.payments.pdf', $payment)
            ->with('sweet-alert', [
                'icon'  => 'success',
                'title' => 'Pago registrado',
                'text'  => 'Se generó el recibo de pago en PDF.',
                'timer' => 2500,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Generate a PDF receipt for a payment.
     */
    public function pdf(SalePayment $payment)
    {
        $payment->load(['sale.customer', 'account']);
        $pdf = Pdf::loadView('admin.sales.payments.receipt', [
            'payment' => $payment,
        ]);
        $filename = sprintf('recibo_pago_%d.pdf', $payment->id);
        return $pdf->download($filename);
    }
}
