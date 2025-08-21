{{-- resources/views/admin/sales/payments/index.blade.php --}}
<x-admin-layout
    title="Pagos de Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Pagos de Ventas', 'href' => route('admin.sales.payments.index')],
    ]"
>
    @livewire('admin.datatables.sale-payment-table')
</x-admin-layout>
