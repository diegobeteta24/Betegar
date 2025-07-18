<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Clientes',  'href' => route('admin.customers.index')],
        ['name' => 'Nuevo'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.customers.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Sólo Tipo de documento y Número de documento en 2 columnas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-wire-native-select name="identity_id" label="Tipo de documento" >
                        @foreach($identities as $identity)
                            <option
                                value="{{ $identity->id }}"
                                {{ old('identity_id') == $identity->id ? 'selected' : '' }}
                            >
                                {{ $identity->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                   
                </div>

                <div>
                    <x-wire-input
                        name="document_number"
                        label="Número de documento"
                        placeholder="Número de documento o CF"
                        value="{{ old('document_number') }}"
                    />
                   
                </div>
            </div>

            {{-- El resto en una sola columna --}}
            <div>
                <x-wire-input
                    name="name"
                    label="Nombre"
                    placeholder="Nombre o razón social"
                    value="{{ old('name') }}"
                />
               
            </div>

            <div>

                 <x-wire-input
                    name="address"
                    label="Dirección"
                    placeholder="Dirección (opcional)"
                    value="{{ old('address') }}"
                />
  
            </div>

            <div>
                <x-wire-input
                    name="email"
                    label="Email"
                    placeholder="Correo electrónico (opcional)"
                    type="email"
                    value="{{ old('email') }}"
                />
              
            </div>

            <div>
                <x-wire-input
                    name="phone"
                    label="Teléfono"
                    placeholder="Teléfono (opcional)"
                    value="{{ old('phone') }}"
                />
               
            </div>

            <div class="flex justify-end">
                <x-button type="submit" blue>
                    Crear Cliente
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
