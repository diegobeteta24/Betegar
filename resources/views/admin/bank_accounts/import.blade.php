{{-- import excel bank accounts --}}
<x-admin-layout
    title="Importar Cuentas Bancarias | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Cuentas Bancarias',   'href' => route('admin.bank-accounts.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-bank-accounts')
</x-admin-layout>
