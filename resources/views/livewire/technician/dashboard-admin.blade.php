<div class="space-y-8">
    @php
        $totalExpenses30 = $techniciansData->sum(fn($d)=>$d['stats']['expenses_30d'] ?? 0);
        $totalCompleted30 = $techniciansData->sum(fn($d)=>$d['stats']['completed_orders_30d'] ?? 0);
        $avgSessionsAggregate = round(collect($techniciansData)->avg(fn($d)=>$d['stats']['avg_sessions_day_30d'] ?? 0),2);
    @endphp
    <div class="grid gap-6 sm:grid-cols-3">
        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-500 mb-1">Gasto total técnicos 30d</div>
            <div class="text-2xl font-bold text-emerald-600">Q {{ number_format($totalExpenses30,2) }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-500 mb-1">Órdenes completadas 30d</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $totalCompleted30 }}</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-xs text-gray-500 mb-1">Sesiones prom. / día (global 30d)</div>
            <div class="text-2xl font-bold text-amber-600">{{ $avgSessionsAggregate }}</div>
        </div>
    </div>
    <hr class="my-4" />
    @foreach($techniciansData as $data)
        <div class="bg-white border rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">Técnico: {{ $data['technician']->name }} ({{ $data['technician']->email }})</h3>
            <div class="grid gap-4 md:grid-cols-3 mb-4">
                <div class="p-3 rounded border bg-gray-50">
                    <div class="text-xs text-gray-500">Gasto 30d</div>
                    <div class="text-lg font-semibold text-emerald-600">Q {{ number_format($data['stats']['expenses_30d'],2) }}</div>
                </div>
                <div class="p-3 rounded border bg-gray-50">
                    <div class="text-xs text-gray-500">Órdenes Completadas 30d</div>
                    <div class="text-lg font-semibold text-indigo-600">{{ $data['stats']['completed_orders_30d'] }}</div>
                </div>
                <div class="p-3 rounded border bg-gray-50">
                    <div class="text-xs text-gray-500">Sesiones Prom. / Día (30d)</div>
                    <div class="text-lg font-semibold text-amber-600">{{ $data['stats']['avg_sessions_day_30d'] }}</div>
                </div>
            </div>
            <div class="mb-2">
                <span class="font-medium">Último check-in:</span>
                @if($data['lastSession'])
                    {{ $data['lastSession']->started_at->timezone(config('app.tz_guatemala','America/Guatemala'))->format('d/m/Y H:i') }}
                    <span class="text-gray-500 text-xs">Lat: {{ $data['lastSession']->start_latitude }}, Lng: {{ $data['lastSession']->start_longitude }}</span>
                @else
                    <span class="text-gray-500">No hay check-ins registrados.</span>
                @endif
            </div>
            <div class="mb-2">
                <span class="font-medium">Órdenes pendientes:</span>
                @if(count($data['pendingOrders'])===0)
                    <span class="text-gray-500">Ninguna</span>
                @else
                    <ul class="list-disc ml-6">
                        @foreach($data['pendingOrders'] as $order)
                            <li>
                                #{{ $order->id }} - {{ $order->customer->name }} ({{ $order->address }})
                                <a href="/work-orders/{{ $order->id }}" class="text-indigo-600 hover:underline ml-2">Ver</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div>
                <span class="font-medium">Solicitudes recientes:</span>
                @if(count($data['recentRequests'])===0)
                    <span class="text-gray-500">Ninguna</span>
                @else
                    <ul class="list-disc ml-6">
                        @foreach($data['recentRequests'] as $req)
                            <li>
                                {{ $req->requests }}
                                <span class="text-xs text-gray-500">({{ $req->created_at->timezone(config('app.tz_guatemala','America/Guatemala'))->format('d/m/Y H:i') }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endforeach
</div>
