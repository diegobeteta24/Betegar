{{-- resources/views/admin/bank_transactions/index.blade.php --}}
<x-admin-layout
    title="Transacciones Bancarias | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Transacciones Bancarias', 'href' => route('admin.bank-transactions.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.bank-transactions.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>
    @livewire('admin.datatables.bank-transaction-table')
</x-admin-layout>
