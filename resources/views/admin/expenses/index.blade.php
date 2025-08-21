<x-admin-layout>
    <x-slot name="title">Gastos Técnicos</x-slot>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Gastos de Técnicos</h1>
    </div>
    <div x-data="expenseList()" x-init="init()" class="space-y-4">
        <div class="flex gap-2">
            <input x-model="search" @input.debounce.400ms="load()" type="text" placeholder="Buscar descripción o técnico" class="w-full rounded border p-2">
            <button @click="load()" class="px-4 py-2 bg-primary-600 text-white rounded">Buscar</button>
        </div>
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr class="text-left">
                        <th class="p-2">ID</th>
                        <th class="p-2">Fecha</th>
                        <th class="p-2">Técnico</th>
                        <th class="p-2">Descripción</th>
                        <th class="p-2 text-right">Monto (Q)</th>
                        <th class="p-2">Comprobante</th>
                        <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="e in expenses" :key="e.id">
                        <tr class="border-b last:border-none hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="p-2" x-text="e.id"></td>
                            <td class="p-2" x-text="e.created_at_format"></td>
                            <td class="p-2" x-text="e.technician?.name || '—'"></td>
                            <td class="p-2" x-text="e.description"></td>
                            <td class="p-2 text-right" x-text="parseFloat(e.amount).toFixed(2)"></td>
                            <td class="p-2">
                                <template x-if="e.voucher_url">
                                    <a :href="e.voucher_url" target="_blank" class="text-primary-600 hover:underline">Ver</a>
                                </template>
                                <template x-if="!e.voucher_url">
                                    <span class="text-gray-400">—</span>
                                </template>
                            </td>
                            <td class="p-2">
                                <a :href="routeShow(e.id)" class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-700">Detalle</a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="loading">
                        <td colspan="7" class="p-4 text-center text-gray-500">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function expenseList(){
            return {
                expenses: [],
                loading:false,
                search:'',
                init(){ this.load(); },
                routeShow(id){ return '/admin/expenses/' + id; },
                async load(){
                    this.loading = true;
                    try {
                        const q = this.search ? ('?search=' + encodeURIComponent(this.search)) : '';
                        const res = await fetch('/api/admin/expenses' + q);
                        this.expenses = await res.json();
                    } catch(e){ console.error(e); }
                    this.loading = false;
                }
            }
        }
    </script>
</x-admin-layout>
