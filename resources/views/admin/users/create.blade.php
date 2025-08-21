{{-- Crear usuario --}}

<x-admin-layout title="Usuarios | Betegar" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Usuarios', 'href' => route('admin.users.index')],
    ['name' => 'Nuevo'],
]">
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.users.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <h1 class="text-2xl font-semibold mb-4">
            Nuevo Usuario
        </h1>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid lg:grid-cols-2 gap-4">




                <x-wire-input name="name" label="Nombre de usuario" placeholder="Nombre de usuario"
                    value="{{ old('username') }}" />
                <x-wire-input name="email" label="Email" placeholder="Email" value="{{ old('email') }}" />

                <x-wire-input name="password" type="password" label="Contraseña" placeholder="Contraseña" />

                <x-wire-input name="password_confirmation" type="password" label="Confirmar contraseña"
                    placeholder="Confirmar contraseña" />

            </div>
              <div class="mt-4">
        <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
        <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md">
            @foreach($roles as $role)
                <option value="{{ $role }}" @selected(old('role') == $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
        @error('role') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" 
                blue
                >
                    Crear Usuario
                </x-wire-button>

            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
