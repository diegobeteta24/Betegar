{{-- resources/views/admin/warehouses/edit.blade.php --}}
<x-admin-layout
    title="Almacenes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
        ['name' => 'Editar'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.warehouses.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <form action="{{ route('admin.warehouses.update', $warehouse) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

           
                    <x-wire-input
                        name="name"
                        label="Nombre"
                        placeholder="Nombre del almacén"
                        :value="old('name', $warehouse->name)"
                    />
                    @error('name')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                
                    <x-wire-input
                        name="location"
                        label="Ubicación"
                        placeholder="Ciudad, país (opcional)"
                        :value="old('location', $warehouse->location)"
                    />
                    @error('location')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                

            <div class="flex justify-end">
                <x-button type="submit" blue>
                    Actualizar Almacén
                </x-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
