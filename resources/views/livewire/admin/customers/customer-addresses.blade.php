<div class="space-y-4">
    <h3 class="font-semibold text-sm">Direcciones</h3>
    <div class="grid md:grid-cols-4 gap-3 items-end">
        <div class="md:col-span-1">
            <x-wire-input label="Etiqueta" wire:model.defer="label" placeholder="Casa / Oficina" />
        </div>
        <div class="md:col-span-2">
            <x-wire-input label="Dirección" wire:model.defer="address" placeholder="Dirección completa" />
        </div>
        <div class="md:col-span-1">
            <x-button wire:click="addAddress" wire:loading.attr="disabled">Agregar</x-button>
        </div>
    </div>
    <x-wire-errors />
    <div class="divide-y border rounded bg-white overflow-hidden">
        @forelse($addresses as $addr)
            <div class="p-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2 {{ $addr->is_primary ? 'bg-blue-50' : '' }}">
                <div class="text-sm">
                    <span class="font-medium">{{ $addr->label ?: 'Sin etiqueta' }}</span>
                    <span class="text-gray-500">– {{ $addr->address }}</span>
                    @if($addr->is_primary)
                        <span class="ml-2 inline-block text-xs px-2 py-0.5 bg-blue-600 text-white rounded">Principal</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-xs">
                    @unless($addr->is_primary)
                        <button class="text-blue-600 hover:underline" wire:click="setPrimary({{ $addr->id }})">Hacer principal</button>
                    @endunless
                    <button class="text-red-600 hover:underline" wire:click="deleteAddress({{ $addr->id }})" onclick="return confirm('¿Eliminar dirección?')">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="p-3 text-sm text-gray-500">Sin direcciones aún.</div>
        @endforelse
    </div>
</div>
