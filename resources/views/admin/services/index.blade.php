<x-admin-layout
    title="Servicios | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Servicios',  'href' => route('admin.services.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 w-full mb-4">
            <x-wire-button href="{{ route('admin.services.import') }}" green class="w-full sm:w-auto">
                <i class="fas fa-file-import"></i> Importar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.services.create') }}" blue class="w-full sm:w-auto">
                <i class="fas fa-plus"></i> Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    @livewire('admin.services.create-service')
    @livewire('admin.datatables.service-table')
</x-admin-layout>
