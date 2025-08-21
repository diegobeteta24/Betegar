<div class="bg-white border rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-3">Órdenes pendientes</h3>
    @if(count($orders) === 0)
        <p class="text-gray-500">No tienes órdenes pendientes.</p>
    @else
        <ul class="divide-y">
            @foreach($orders as $order)
                <li class="py-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium">#{{ $order->id }} - {{ $order->customer->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->address }}</div>
                    </div>
                    <a href="{{ url('/work-orders/'.$order->id) }}" class="text-indigo-600 hover:underline">Ver</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
