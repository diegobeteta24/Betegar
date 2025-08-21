{{-- resources/views/admin/roles/create.blade.php --}}

<x-admin-layout title="Roles | Betegar" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Roles',     'href' => route('admin.roles.index')],
    ['name' => 'Nuevo'],
]">
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.roles.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>

    <x-wire-card>
        <h1 class="text-2xl font-semibold mb-4">
            Nuevo Rol
        </h1>

        <form action="{{ route('admin.roles.store') }}"
         method="POST"
         class="space-y-4">
            @csrf

            <x-wire-input
                name="name"
                label="Nombre del rol"
                placeholder="Escribe el nombre del rol"
                value="{{ old('name') }}"
            />
             

                <div>
                    <p class="block text-sm font-medium disabled:opacity-60 text-gray-700 dark:text-gray-400 invalidated:text-negative-600 dark:invalidated:text-negative-700">
                       Permisos:
                    </p>

                    <ul class="columns-1 md:columns-2 lg:columns-4 gap-4">
                        {{-- Loop through permissions --}}
                        @foreach ($permissions as $permission)
                            <li>
                                <label>

                                  <x-checkbox
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        :checked="in_array($permission->id, old('permissions', []))"
                                    />
                                    <span class=" text-sm text-gray-700 dark:text-gray-400">
                                        {{ $permission->name }}
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>

                </div>
            

            <div class="flex justify-end">
                <x-wire-button type="submit" blue>
                    Crear Rol
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
