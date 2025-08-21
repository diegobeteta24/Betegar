{{-- import excel expense categories --}}
<x-admin-layout
    title="Importar Categorías de Gasto | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Categorías de gasto',   'href' => route('admin.expense-categories.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-expense-categories')
</x-admin-layout>
