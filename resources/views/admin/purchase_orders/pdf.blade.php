{{-- resources/views/admin/purchases/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Orden de Compra</title>
    <style>
        /*** Tipografía y reset ***/
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1, h2, h3, h4, h5, h6 { margin: 0; }

        /*** Encabezado con logo ***/
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #4a5568;
        }
        .header .logo img {
            max-height: 60px;
        }
        .header .company-info {
            text-align: right;
            font-size: 14px;
            line-height: 1.2;
        }

        /*** Título del documento ***/
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #2d3748;
        }

        /*** Datos de la compra ***/
        .metadata {
            padding: 0 20px;
            line-height: 1.5;
        }
        .metadata strong { width: 120px; display: inline-block; }

        /*** Tabla de productos ***/
        .items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items th,
        .items td {
            border: 1px solid #cbd5e0;
            padding: 8px;
        }
        .items th {
            background-color: #edf2f7;
            font-weight: 600;
            text-align: center;
        }
        .items tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .items td {
            vertical-align: middle;
        }
        .items .text-right {
            text-align: right;
        }

        /*** Total ***/
        .total {
            padding: 0 20px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        /*** Pie de página ***/
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding: 8px 0;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo Empresa">
        </div>
        <div class="company-info">
            <strong>Mi Empresa S.A.</strong><br>
            Av. Principal 123<br>
            Ciudad, País<br>
            T: +51 123 456 789
        </div>
    </div>

    {{-- Título --}}
    <div class="title">
        Detalle de la Orden de Compra<br>
        <small>#{{ $model->serie }}-{{ str_pad($model->correlative, 4, '0', STR_PAD_LEFT) }}</small>
    </div>

    {{-- Metadatos --}}
    <div class="metadata">
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($model->date)->format('d/m/Y') }}</p>
        <p><strong>Proveedor:</strong> {{ $model->supplier->name ?? '—' }}</p>
        <p><strong>Observación:</strong> {{ $model->observation ?? '—' }}</p>
    </div>

    {{-- Tabla de productos --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Producto</th>
                <th style="width: 15%;">Cantidad</th>
                <th style="width: 20%;">Precio Unitario</th>
                <th style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model->products as $i => $product)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                    <td class="text-right">Q/ {{ number_format($product->pivot->price, 2) }}</td>
                    <td class="text-right">Q/ {{ number_format($product->pivot->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="total">
        Total: Q/ {{ number_format($model->total, 2) }}
    </div>

    {{-- Pie de página --}}
    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} — Página <span class="page"></span>
    </div>

</body>
</html>
