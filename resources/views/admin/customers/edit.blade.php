{{-- resources/views/admin/customers/edit.blade.php --}}
<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Clientes',  'href' => route('admin.customers.index')],
        ['name' => 'Editar'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.customers.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <form action="{{ route('admin.customers.update', $customer) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Sólo Tipo de documento y Número de documento en 2 columnas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de documento</label>
                        <x-wire-native-select name="identity_id" label="Tipo de documento">
                            @foreach($identities as $identity)
                                <option value="{{ $identity->id }}" {{ old('identity_id', $customer->identity_id) == $identity->id ? 'selected' : '' }}>{{ $identity->name }}</option>
                            @endforeach
                        </x-wire-native-select>
                    @error('identity_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <x-wire-input
                        name="document_number"
                        label="Número de documento"
                        placeholder="Número de documento o CF"
                        :value="old('document_number', $customer->document_number)"
                    />
                    @error('document_number')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- El resto en una sola columna --}}
            <div>
                <x-wire-input
                    name="name"
                    label="Nombre"
                    placeholder="Nombre o razón social"
                    :value="old('name', $customer->name)"
                />
                @error('name')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="address" value="{{ old('address', $customer->address) }}" />

            <div>
                <x-wire-input
                    name="email"
                    label="Email"
                    placeholder="Correo electrónico (opcional)"
                    type="email"
                    :value="old('email', $customer->email)"
                />
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-wire-input
                    name="phone"
                    label="Teléfono"
                    placeholder="Teléfono (opcional)"
                    :value="old('phone', $customer->phone)"
                />
                @error('phone')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t" x-data="customerAddresses({ existing: @js($customer->addresses()->orderByDesc('is_primary')->orderBy('id')->get(['id','label','address','is_primary'])) })">
                <h3 class="font-semibold text-sm mb-3">Direcciones múltiples</h3>
        <template x-for="(addr,idx) in addresses" :key="addr.uid">
            <div class="grid md:grid-cols-12 gap-2 mb-2 items-start p-3 border rounded bg-white">
                <input type="hidden" :name="`addresses[${idx}][id]`" x-model="addr.id">
                <div class="md:col-span-2">
                    <input type="text" placeholder="Etiqueta" class="w-full border-gray-300 rounded text-xs" x-model="addr.label" :name="`addresses[${idx}][label]`" />
                </div>
                <div class="md:col-span-8">
                    <input type="text" placeholder="Dirección" class="w-full border-gray-300 rounded text-xs" x-model="addr.address" :name="`addresses[${idx}][address]`" required />
                </div>
                <div class="md:col-span-1 flex flex-col justify-center mt-6">
                    <label class="inline-flex items-center space-x-1 text-xs">
                        <input type="radio" name="primary_address"
                               :value="addr.uid" x-model="primaryUid"
                               @change="markPrimary(addr.uid)">
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
            <button type="button" class="text-xs px-3 py-1 bg-gray-100 rounded hover:bg-gray-200" @click="add()">+ Añadir dirección</button>
        </div>
        <p class="text-xs text-gray-500 mt-2">La dirección marcada como principal sincroniza el campo rápido arriba.</p>
        <script>
            function customerAddresses({existing}) {
                return {
                    addresses: existing.map(a => ({...a, uid: 'ex-'+a.id})),
                    primaryUid: (existing.find(a=>a.is_primary)?.id ? 'ex-'+ existing.find(a=>a.is_primary).id : null),
                    init(){ if(this.addresses.length===0){ this.add(); } },
                    add() {
                        const uid = 'new-'+Date.now()+Math.random().toString(36).slice(2);
                        this.addresses.push({id: null,label:'',address:'',is_primary:false,uid});
                        if(!this.primaryUid) this.primaryUid = uid;
                    },
                    remove(index) {
                        const rem = this.addresses.splice(index,1)[0];
                        if(this.primaryUid === rem.uid) {
                            this.primaryUid = this.addresses[0]?.uid || null;
                        }
                    },
                    markPrimary(uid){ this.primaryUid = uid; this.syncLegacy(); },
                    syncLegacy(){
                        const legacy = document.querySelector('input[name="address"]');
                        const current = this.addresses.find(a=>a.uid===this.primaryUid);
                        if(legacy && current) legacy.value = current.address;
                    }
                }
            }
        </script>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" blue>
                    Actualizar Cliente
                </x-button>
            </div>
        </form>
    </x-wire-card>

    @push('js')
    <script>
      const select = document.querySelector('select[name="identity_id"]');
      const doc    = document.querySelector('input[name="document_number"]');
      function toggleCF() {
        if (parseInt(select.value) === 1) {
          doc.value    = 'CF';
          doc.readOnly = true;
        } else {
          if (doc.value === 'CF') doc.value = '';
          doc.readOnly = false;
        }
      }
      select.addEventListener('change', toggleCF);
      document.addEventListener('DOMContentLoaded', toggleCF);
    </script>
    @endpush

</x-admin-layout>
