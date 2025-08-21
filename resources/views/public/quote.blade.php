<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Cotización #{{ $quote->serie }}-{{ str_pad($quote->correlative,4,'0',STR_PAD_LEFT) }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
	<style>
		body, h1, h2, h3, p { margin:0; padding:0; }
		body { font-family:'Montserrat',sans-serif; background:#eef2f7; color:#333; line-height:1.4; }
		.container { width:90%; max-width:900px; margin:20px auto; }
		.header { text-align:center; padding:20px 0; }
		.header img { max-height:80px; }
		.banner { background:#5e2129; color:#fff; padding:30px 20px; border-radius:8px; text-align:left; }
		.banner h1 { font-size:1.8em; margin-bottom:10px; }
		.banner .meta { font-size:.95em; opacity:.85; }
		.expiration-alert { background:#ffe6e6; color:#cc0000; border:1px solid #cc0000; padding:10px; border-radius:4px; margin:15px 0; font-weight:bold; }
		.info-wrapper { display:flex; justify-content:space-between; flex-wrap:wrap; margin-top:20px; }
		.info-block { flex:1 1 45%; margin-bottom:15px; }
		.info-block p { margin:6px 0; font-size:.95em; display:flex; align-items:center; }
		.info-block p strong { width:110px; flex-shrink:0; margin-right:8px; }
		.info-block p span { flex:1; word-break:break-word; }
		.comments { background:#5e2129; color:#fff; padding:15px; border-radius:4px; margin-top:20px; font-style:italic; font-size:.95em; }
		.items-section { margin:30px 0; }
		.items-section h2 { text-align:center; margin-bottom:20px; font-size:1.4em; }
		.item-card { background:#fff; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1); padding:15px; margin-bottom:15px; display:flex; justify-content:space-between; flex-wrap:wrap; }
		.item-info { flex:1 1 60%; }
		.item-info h3 { font-size:1.1em; margin-bottom:5px; }
		.item-info p { margin:3px 0; color:#555; font-size:.95em; }
		.item-price { text-align:right; min-width:120px; font-weight:bold; font-size:1em; }
		.totals { background:#fff; padding:15px; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1); max-width:400px; margin:20px auto; text-align:right; font-size:.95em; }
		.totals p { margin:6px 0; }
		.totals .totals-label { margin-right:10px; }
		.payment-conditions { background:#fff; padding:15px; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1); margin:30px auto; max-width:700px; font-size:.95em; line-height:1.5em; }
		.payment-conditions h3 { margin-bottom:10px; }
		.footer { background:#5e2129; color:#fff; text-align:center; padding:20px; border-radius:8px; margin-top:30px; }
		.footer p { margin:4px 0; font-size:.95em; }
		.footer .btn { background:#000; border:none; padding:10px 20px; color:#fff; border-radius:4px; margin:10px 5px 0; cursor:pointer; font-size:.95em; }
		.footer .btn:hover { background:#333; }
		@media print { .footer .btn{display:none;} }
		@media (max-width:576px) { .info-block, .item-card { flex:1 1 100%; } }
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<img src="{{ asset('logo.png') }}" alt="Logo">
		</div>
		@php
			$numero = $quote->serie.'-'.str_pad($quote->correlative,4,'0',STR_PAD_LEFT);
			$creationDateFormatted = optional($quote->date)->format('d/m/Y');
			$expirationDateFormatted = '';
			$customer = $quote->customer;
			$direccionText = $customer->address ?? 'N/A';
			$discountPercentage = $quote->discount_percent ?? 0;
		@endphp
		<div class="banner">
			<h1>Cotización #{{ $numero }}</h1>
			<div class="meta">Creada: {{ $creationDateFormatted }} @if($expirationDateFormatted) — Vence: {{ $expirationDateFormatted }} @endif</div>
			@if(false)
				<div class="expiration-alert">¡Esta cotización ha vencido!</div>
			@endif
			<div class="info-wrapper">
				<div class="info-block">
					<p><strong>Cliente:</strong><span>{{ $customer->name ?? 'N/A' }}</span></p>
					<p><strong>Dirección:</strong><span>{{ $direccionText }}</span></p>
					<p><strong>Teléfono:</strong><span>{{ $customer->phone ?? 'N/A' }}</span></p>
					<p><strong>Email:</strong><span>{{ $customer->email ?? 'N/A' }}</span></p>
				</div>
				<div class="info-block">
					<p><strong>Observación:</strong><span>{{ $quote->observation ?? '—' }}</span></p>
				</div>
			</div>
			<div class="comments">
				<h3>Comentarios</h3>
				<p>{{ $quote->observation ?? 'Sin comentarios' }}</p>
			</div>
		</div>
		<div class="items-section">
			<h2>Productos y Servicios</h2>
			@forelse($quote->products as $p)
				@php $pivot = $p->pivot; @endphp
				<div class="item-card">
					<div class="item-info">
						<h3>{{ $p->name }} @if($p->type==='service')<span style="font-size:.7em; background:#eee; padding:2px 6px; border-radius:4px; margin-left:6px; font-weight:normal;">Servicio</span>@endif</h3>
						@if($pivot->description)
							<p style="white-space:pre-line; color:#444;">{{ $pivot->description }}</p>
						@endif
						<p><strong>Cantidad:</strong> <span>{{ $pivot->quantity }}</span></p>
						<p><strong>Descuento:</strong> <span>{{ number_format($discountPercentage,2) }}%</span></p>
					</div>
						<div class="item-price">
							{{ $pivot->quantity }} × Q{{ number_format($pivot->price,2) }}<br>
							<small>Total: Q{{ number_format($pivot->subtotal,2) }}</small>
						</div>
				</div>
			@empty
				<p style="text-align:center;color:#666;font-size:.9em">Sin productos.</p>
			@endforelse
		</div>
		<div class="totals">
			@php
				// Fallback: si los campos guardados quedaron en 0 (cotizaciones antiguas) recalculamos desde los productos.
				$computedSubtotal = $quote->products->sum(fn($p)=> ($p->pivot->quantity ?? 0) * ($p->pivot->price ?? 0));
				$displaySubtotal = ($quote->subtotal ?? 0) > 0 ? $quote->subtotal : $computedSubtotal;
				$discountPercent = $quote->discount_percent ?? 0;
				$displayDiscountAmount = ($quote->discount_amount ?? 0) > 0
					? $quote->discount_amount
					: round($displaySubtotal * ($discountPercent/100), 2);
				$displayTotal = ($quote->total ?? 0) > 0
					? $quote->total
					: max($displaySubtotal - $displayDiscountAmount, 0);
			@endphp
			<p><span class="totals-label">Subtotal:</span> Q{{ number_format($displaySubtotal,2) }}</p>
			<p><span class="totals-label">Descuento ({{ number_format($discountPercent,2) }}%)</span> -Q{{ number_format($displayDiscountAmount,2) }}</p>
			<p style="font-weight:bold;"><span class="totals-label">Total:</span> Q{{ number_format($displayTotal,2) }}</p>
		</div>
		<div class="payment-conditions">
			<h3>Condiciones</h3>
			<p>No especificadas</p>
		</div>
		<div class="footer">
			<p><strong>© {{ date('Y') }} Betegar</strong></p>
			<button class="btn" onclick="window.print()">Imprimir</button>
		</div>
	</div>
</body>
</html>
