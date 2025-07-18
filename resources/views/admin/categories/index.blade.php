<x-admin-layout
    title="Categorias | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Categorias',  'href' => route('admin.categories.index')],
    ]"
>
    {{-- Slot para el botón “Nuevo” (se posiciona en el header del layout) --}}
     <x-slot name="action">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 w-full mb-4">
            <x-wire-button
                href="{{ route('admin.categories.import') }}"
                green
                class="w-full sm:w-auto"
            >
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>

            <x-wire-button
                href="{{ route('admin.categories.create') }}"
                blue
                class="w-full sm:w-auto"
            >
                <i class="fas fa-plus"></i>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>


    {{-- Contenedor responsive para la tabla --}}
    <div class="overflow-x-auto">
        @livewire('admin.datatables.category-table')
    </div>
</x-admin-layout>
