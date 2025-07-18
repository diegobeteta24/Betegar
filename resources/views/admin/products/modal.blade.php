
{{-- Modal para mostrar stock --}}

 
<x-wire-modal-card title="Stock por almacen" wire:model="openModal">

    <ul class="space-y-3">

        @forelse ($inventories as $inventory)
            <li class="flex items-center justify-between p-4 bg-gray-100 rounded-lg shadow-sm">
            <div>
                <p class="text-sm text-gray-600">
                    {{ $inventory->warehouse->name ?? 'Almacen no disponible' }}
                </p>

                <p class="font-medium text-gray-800">
                    {{ $inventory->warehouse->location ?? 'Almacen no disponible' }}
                </p>
            </div>
            <div class="text-right">
           <p class="text-sm text-gray-500">
              Stock disponible: 
           </p>
           <p class="text-lg font-bold {{ $inventory->quantity_balance > 0 ? 'text-green-600' : 'text-red-600' }}">
               {{-- Mostrar cantidad balanceada --}}
            {{ $inventory->quantity_balance  }}
           </p>

            </div>
            </li>
        @empty
            <li class="text-center text-gray-500 py-6">
                {{-- Mensaje cuando no hay inventarios --}}
                No hay inventarios disponibles.
            </li>
        @endforelse

    </ul>

</x-wire-modal-card>