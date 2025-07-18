<x-admin-layout
    title="Productos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Productos',  'href' => route('admin.products.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 w-full mb-4">
            <x-wire-button
                href="{{ route('admin.products.import') }}"
                green
                class="w-full sm:w-auto"
            >
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>

            <x-wire-button
                href="{{ route('admin.products.create') }}"
                blue
                class="w-full sm:w-auto"
            >
                <i class="fas fa-plus"></i>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    @livewire('admin.datatables.product-table')

    @push('css')
        <style>
            .image-product {
                width: 5rem !important;
                height: 2.5rem !important;
                object-fit: cover !important;
                object-position: center !important;
            }
        </style>
    @endpush
</x-admin-layout>
