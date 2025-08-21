<div class="p-4 border rounded mb-6 bg-white space-y-3">
    <h3 class="font-semibold text-sm">Crear servicio rápido</h3>
    <div class="grid md:grid-cols-4 gap-3 items-start">
        <div class="md:col-span-1">
            <x-wire-input label="Nombre" wire:model.defer="name" placeholder="Nombre" />
        </div>
        <div class="md:col-span-1">
            <x-wire-input label="Precio" type="number" step="0.01" wire:model.defer="price" placeholder="0.00" />
        </div>
        <div class="md:col-span-1">
            <x-wire-native-select label="Categoría" wire:model="category_id">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </x-wire-native-select>
        </div>
        <div class="md:col-span-1 flex items-end">
            <x-button wire:click="save" wire:loading.attr="disabled">Guardar</x-button>
        </div>
        <div class="md:col-span-4">
            <x-wire-textarea rows="2" label="Descripción" wire:model.defer="description" placeholder="Descripción (opcional)" />
        </div>
    </div>
    <x-wire-errors />
</div>
