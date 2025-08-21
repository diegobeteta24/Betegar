{{-- resources/views/admin/work_orders/create.blade.php --}}
<x-admin-layout
    title="Nueva Orden de Trabajo | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Trabajo', 'href' => route('admin.work-orders.index')],
        ['name' => 'Nueva'],
    ]"
>
    <x-wire-card class="max-w-3xl mx-auto" x-data="(()=>({
        customerId:'',
        addresses:[],
        address:'',
        loading:false,
        fetchAddresses(){
            this.addresses=[];
            if(!this.customerId){ return; }
            this.loading=true;
            fetch(`/api/customers/${this.customerId}/addresses`)
                .then(r=>r.ok?r.json():[])
                .then(json=>{ this.addresses=json||[]; if(this.addresses.length){ const primary=this.addresses.find(a=>a.is_primary); if(primary){ this.address=primary.address; } } })
                .catch(()=>{})
                .finally(()=>this.loading=false);
        },
        useAddress(addr){ this.address = addr; },
        beforeSubmit(){},
    }))()">
        <form method="POST" action="{{ route('admin.work-orders.store') }}" class="space-y-6" @submit="beforeSubmit()">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Cliente</label>
                    <select name="customer_id" x-model="customerId" @change="fetchAddresses()" class="w-full border rounded px-2 py-2">
                        <option value="">-- seleccionar --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Dirección</label>
                    <input type="text" name="address" x-model="address" class="w-full border rounded px-3 py-2" placeholder="Dirección" />
                    <template x-if="addresses.length">
                        <div class="mt-2 space-y-1 max-h-32 overflow-auto border rounded p-2 bg-gray-50">
                            <template x-for="a in addresses" :key="a.id">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="text-xs">
                                        <span class="font-medium" x-text="a.label"></span>
                                        <span x-show="a.is_primary" class="ml-1 text-emerald-600 font-semibold">(Principal)</span>
                                        <div class="text-gray-600" x-text="a.address"></div>
                                    </div>
                                    <button type="button" class="text-xs px-2 py-1 rounded bg-emerald-600 text-white hover:bg-emerald-500" @click="useAddress(a.address)">Usar</button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            <div>
                <x-wire-textarea name="objective" label="Objetivo" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-native-select name="status" label="Estado">
                    <option value="pending">Pendiente</option>
                    <option value="in_progress">En progreso</option>
                    <option value="done">Completada</option>
                    <option value="cancelled">Cancelada</option>
                </x-wire-native-select>
                <div>
                    <label class="block text-sm font-medium mb-1">Técnicos (selecciona uno o más)</label>
                    <div class="max-h-48 overflow-auto border rounded p-2 space-y-1 bg-white">
                        @foreach($technicians as $t)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="technicians[]" value="{{ $t->id }}" class="rounded border-gray-300 focus:ring-indigo-500">
                                <span>{{ $t->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Ya no necesitas CTRL: sólo marca los técnicos.</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <x-wire-button href="{{ route('admin.work-orders.index') }}" gray class="w-full sm:w-auto order-2 sm:order-1">
                    Cancelar
                </x-wire-button>
                <x-wire-button type="submit" icon="check" green class="w-full sm:w-auto order-1 sm:order-2">
                    Crear orden
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>
    <!-- Script inline eliminado: x-data lleva toda la lógica -->
</x-admin-layout>
