{{-- resources/views/admin/roles/edit.blade.php --}}

<x-admin-layout title="Roles | Betegar" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Roles',     'href' => route('admin.roles.index')],
    ['name' => 'Editar'],
]">
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.roles.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <h1 class="text-2xl font-semibold mb-4">Editar Rol</h1>

        <form action="{{ route('admin.roles.update', $role) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

            <x-wire-input
                name="name"
                label="Nombre del rol"
                placeholder="Escribe el nombre del rol"
                value="{{ old('name', $role->name) }}"
            />

            <div>
                <p class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                    Permisos:
                </p>

                @php
                    $selected = old('permissions', $role->permissions->pluck('id')->toArray());
                @endphp

                <ul class="columns-1 md:columns-2 lg:columns-4 gap-4">
                    @foreach ($permissions as $permission)
                        <li>
                            <label class="inline-flex items-center space-x-2">
                                <x-checkbox
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    :checked="in_array($permission->id, $selected)"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-400">
                                    {{ $permission->name }}
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                @error('permissions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-6 space-x-2">
                <x-wire-button type="submit" blue>
                    Actualizar Rol
                </x-wire-button>
                
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
