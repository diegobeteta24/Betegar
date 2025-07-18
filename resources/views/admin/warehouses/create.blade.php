{{-- resources/views/admin/warehouses/create.blade.php --}}
<x-admin-layout
    title="Almacenes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
        ['name' => 'Nuevo'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.warehouses.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <form action="{{ route('admin.warehouses.store') }}"
              method="POST"
              class="space-y-4">
            @csrf

            
                
                    <x-wire-input
                        name="name"
                        label="Nombre"
                        placeholder="Nombre del almacén"
                        value="{{ old('name') }}"
                    />
            
                    <x-wire-input
                        name="location"
                        label="Ubicación"
                        placeholder="Ciudad, país (opcional)"
                        value="{{ old('location') }}"
                    />
                 
            

            <div class="flex justify-end">
                <x-button type="submit" blue>
                    Crear Almacén
                </x-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
