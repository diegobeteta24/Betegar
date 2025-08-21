<x-admin-layout
    title="Importar Servicios | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Servicios', 'href' => route('admin.services.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-services')
</x-admin-layout>
