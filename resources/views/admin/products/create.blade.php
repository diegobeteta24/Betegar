<x-admin-layout
    title="Productos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Productos', 'href' => route('admin.products.index')],
        ['name' => 'Nuevo'],
    ]"
>

    <x-wire-card>
        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
            @csrf

            <x-wire-input
                name="name"
                label="Nombre"
                placeholder="Nombre del producto"
                value="{{ old('name') }}"
                
            />

            <x-wire-textarea
                name="description"
                label="Descripción"
                placeholder="Descripción del producto"
            >{{ old('description') }}</x-wire-textarea>

    

            <x-wire-input
                name="price"
                type="number"
                step="0.01"
                label="Precio"
                placeholder="Precio unitario"
                value="{{ old('price') }}"
               
            />

            {{-- Selector de categoría --}}
            
                
                <x-wire-native-select
                

                 label="Categoría"
                    name="category_id"
                >
                    

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>
                @error('category_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            
    

            <div class="flex justify-end">
    <x-button type="submit">
        Crear
    </x-button>
</div>
        </form>
    </x-wire-card>
</x-admin-layout>
