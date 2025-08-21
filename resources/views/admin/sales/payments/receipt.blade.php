{{-- resources/views/admin/sales/payments/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color:#333 }
        .header { display:flex; justify-content:space-between; align-items:center; padding:16px; border-bottom:2px solid #4a5568; }
        .logo img { max-height:50px }
        .title { text-align:center; font-size:18px; font-weight:bold; margin:16px 0; }
        .meta { padding: 0 16px; line-height:1.5 }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:8px }
        .box { padding:8px; border:1px solid #cbd5e0; }
        .footer { position:fixed; bottom:0; left:0; right:0; text-align:center; font-size:10px; color:#718096; border-top:1px solid #e2e8f0; padding:6px 0 }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div class="company-info" style="text-align:right">
            <strong>Betegar</strong><br>
            Guatemala<br>
        </div>
    </div>

    <div class="title">Recibo de Pago</div>

    <div class="meta">
        <div class="grid">
            <div class="box">
                <strong>Venta:</strong> #{{ $payment->sale->serie }}-{{ str_pad($payment->sale->correlative ?? $payment->sale->id, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Cliente:</strong> {{ $payment->sale->customer->name ?? '—' }}<br>
                <strong>Fecha Venta:</strong> {{ optional($payment->sale->date)->format('d/m/Y') }}
            </div>
            <div class="box">
                <strong>Pago ID:</strong> {{ $payment->id }}<br>
                <strong>Método:</strong> {{ strtoupper($payment->method) }}<br>
                <strong>Referencia:</strong> {{ $payment->reference ?? '—' }}<br>
                <strong>Fecha Pago:</strong> {{ optional($payment->paid_at)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="box" style="margin-top:12px">
            <strong>Cuenta:</strong> {{ $payment->account->name }} ({{ $payment->account->currency }})<br>
            <strong>Monto:</strong> Q {{ number_format($payment->amount, 2) }}
        </div>
    </div>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
