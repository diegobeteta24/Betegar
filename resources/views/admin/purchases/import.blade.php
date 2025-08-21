<x-admin-layout
    title="Importar Compras | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Compras', 'href' => route('admin.purchases.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-purchases')
</x-admin-layout>
