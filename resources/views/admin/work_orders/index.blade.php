{{-- resources/views/admin/work_orders/index.blade.php --}}
<x-admin-layout
    title="Órdenes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Trabajo', 'href' => route('admin.work-orders.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <x-wire-button href="{{ route('admin.work-orders.create') }}" blue class="w-full sm:w-auto order-2 sm:order-1">
                Nueva orden
            </x-wire-button>
            <x-wire-button href="{{ route('admin.work-orders.import') }}" purple class="w-full sm:w-auto order-1 sm:order-2">
                Importar
            </x-wire-button>
        </div>
    </x-slot>

    <div class="relative">
        <div class="block sm:hidden mb-3 text-xs text-gray-500">Desliza horizontalmente para ver todas las columnas.</div>
        <div class="overflow-x-auto scrollbar-thin">
            @livewire('admin.datatables.work-order-table')
        </div>
    </div>
    @push('css')
        <style>
            .scrollbar-thin::-webkit-scrollbar{height:6px;width:6px}
            .scrollbar-thin::-webkit-scrollbar-track{background:transparent}
            .scrollbar-thin::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:3px}
            @media(max-width:640px){
                .rwt-table table{font-size:12px}
                .rwt-table thead th{white-space:nowrap}
                .rwt-table td{white-space:nowrap}
            }
        </style>
    @endpush
</x-admin-layout>
