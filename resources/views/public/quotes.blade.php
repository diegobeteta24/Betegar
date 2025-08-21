<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotizaciones Públicas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex" />
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,sans-serif;margin:0;background:#f5f6f8;color:#222}
        header{background:#5e2129;color:#fff;padding:14px 20px}
        h1{margin:0;font-size:1.3rem}
        main{width:92%;max-width:980px;margin:20px auto}
        table{width:100%;border-collapse:collapse;background:#fff}
        th,td{padding:8px 10px;border-bottom:1px solid #eee;text-align:left;font-size:13px}
        th{background:#fafafa;font-weight:600}
        a.button{display:inline-block;background:#5e2129;color:#fff;text-decoration:none;padding:6px 10px;border-radius:4px;font-size:12px}
        .tag{background:#e5e7eb;padding:2px 6px;border-radius:4px;font-size:11px}
        footer{margin:40px 0 20px;text-align:center;font-size:12px;color:#555}
        @media (max-width:640px){ table,thead,tbody,tr,td,th{display:block} th{position:sticky;top:0} tr{margin-bottom:10px;border:1px solid #ddd;border-radius:6px;overflow:hidden} td{border:none;border-bottom:1px solid #eee} td:last-child{border-bottom:none} }
    </style>
</head>
<body>
    <header>
        <h1>Cotizaciones Públicas</h1>
    </header>
    <main>
        <p style="font-size:13px;color:#555">Listado rápido de cotizaciones con enlace público (máx 200 más recientes).</p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Correlativo</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Enlace</th>
                </tr>
            </thead>
            <tbody>
            @forelse($quotes as $q)
                <tr>
                    <td>{{ $q->id }}</td>
                    <td>{{ optional($q->date)->format('Y-m-d') }}</td>
                    <td>{{ $q->serie }}</td>
                    <td>{{ $q->correlative }}</td>
                    <td>{{ optional($q->customer)->name }}</td>
                    <td>Q {{ number_format($q->total,2,'.',',') }}</td>
                    <td>
                        <a class="button" target="_blank" href="{{ route('public.quote.show',$q->public_token) }}">Ver</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#777">No hay cotizaciones públicas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </main>
    <footer>&copy; {{ date('Y') }} Betegar</footer>
</body>
</html>
