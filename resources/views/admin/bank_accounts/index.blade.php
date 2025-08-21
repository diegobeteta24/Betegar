{{-- resources/views/admin/bank_accounts/index.blade.php --}}
<x-admin-layout
    title="Cuentas Bancarias | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Cuentas Bancarias', 'href' => route('admin.bank-accounts.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex gap-2">
            <x-wire-button href="{{ route('admin.bank-accounts.import') }}" gray>
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.bank-accounts.create') }}" blue>
                Nueva cuenta
            </x-wire-button>
        </div>
    </x-slot>

    @livewire('admin.datatables.bank-account-table')

</x-admin-layout>
