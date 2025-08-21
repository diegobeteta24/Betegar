{{-- import excel quotes --}}
<x-admin-layout
    title="Importar Cotizaciones | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Cotizaciones',   'href' => route('admin.quotes.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-quotes')
</x-admin-layout>
