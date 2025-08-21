{{-- resources/views/admin/customers/create.blade.php --}}
<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Clientes',  'href' => route('admin.customers.index')],
        ['name' => 'Nuevo'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.customers.index') }}" gray>Volver</x-wire-button>
    </x-slot>

        <x-wire-card>
                <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-6"
                            x-data="{
                                addresses: [],
                                primaryUid: null,
                                add(){ const uid='new-'+Date.now()+Math.random().toString(36).slice(2); this.addresses.push({id:null,label:'',address:'',is_primary:false,uid}); if(!this.primaryUid){ this.primaryUid=uid; this.syncLegacy(); } },
                                remove(i){ const rem=this.addresses.splice(i,1)[0]; if(this.primaryUid===rem.uid){ this.primaryUid=this.addresses[0]?.uid||null; this.syncLegacy(); } },
                                syncLegacy(){ const legacy=this.$refs.legacyAddress; const curr=this.addresses.find(a=>a.uid===this.primaryUid); if(legacy && curr){ legacy.value=curr.address; } },
                                init(){ if(this.addresses.length===0){ this.add(); } }
                            }" x-init="init()">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-wire-native-select name="identity_id" label="Tipo de documento" required>
                        @foreach($identities as $identity)
                            <option value="{{ $identity->id }}" {{ old('identity_id') == $identity->id ? 'selected' : '' }}>{{ $identity->name }}</option>
                        @endforeach
                    </x-wire-native-select>
                    @error('identity_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-wire-input name="document_number" label="Número de documento" placeholder="Número de documento o CF" value="{{ old('document_number') }}" />
                    @error('document_number')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <x-wire-input name="name" label="Nombre" placeholder="Nombre o razón social" value="{{ old('name') }}" />
                @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <x-wire-input name="email" label="Email" placeholder="Correo electrónico (opcional)" type="email" value="{{ old('email') }}" />
                @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <x-wire-input name="phone" label="Teléfono" placeholder="Teléfono (opcional)" value="{{ old('phone') }}" />
                @error('phone')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <input type="hidden" name="address" x-ref="legacyAddress" value="{{ old('address') }}" />

            <div class="pt-4 border-t">
                <h3 class="font-semibold text-sm mb-3">Direcciones</h3>
                <template x-for="(addr,idx) in addresses" :key="addr.uid">
                    <div class="grid md:grid-cols-12 gap-2 mb-2 items-start p-3 border rounded bg-white">
                        <div class="md:col-span-2">
                            <input type="text" placeholder="Etiqueta" class="w-full border-gray-300 rounded text-xs" x-model="addr.label" :name="`addresses[${idx}][label]`" />
                        </div>
                        <div class="md:col-span-8">
                            <input type="text" placeholder="Dirección" class="w-full border-gray-300 rounded text-xs" x-model="addr.address" :name="`addresses[${idx}][address]`" required />
                        </div>
                        <div class="md:col-span-1 flex flex-col justify-center mt-6">
                            <label class="inline-flex items-center space-x-1 text-xs">
                                <input type="radio" name="primary_address" :value="addr.uid" x-model="primaryUid" @change="syncLegacy()">
                                <span>Principal</span>
                            </label>
                            <input type="hidden" :name="`addresses[${idx}][is_primary]`" :value="primaryUid===addr.uid ? 1:0">
                        </div>
                        <div class="md:col-span-1 flex justify-end mt-6">
                            <button type="button" class="text-red-600 text-xs hover:underline" @click="remove(idx)" x-show="addresses.length>1">Eliminar</button>
                        </div>
                    </div>
                </template>
                <div class="mt-3">
                    <button type="button" class="text-xs px-3 py-1 bg-gray-100 rounded hover:bg-gray-200" @click="add()">+ Añadir otra</button>
                </div>
                <p class="text-xs text-gray-500 mt-2">La dirección marcada como principal se usará como principal del cliente.</p>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" blue>Crear Cliente</x-button>
            </div>
        </form>
    </x-wire-card>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.querySelector('select[name="identity_id"]');
            const doc    = document.querySelector('input[name="document_number"]');
            function toggleCF() {
                if (select && doc) {
                    if (parseInt(select.value) === 1) {
                        doc.value    = 'CF';
                        doc.readOnly = true;
                    } else {
                        if (doc.value === 'CF') doc.value = '';
                        doc.readOnly = false;
                    }
                }
            }
            select?.addEventListener('change', toggleCF);
            toggleCF();
        });
    </script>
    @endpush
</x-admin-layout>
