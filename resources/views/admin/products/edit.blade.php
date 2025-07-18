<x-admin-layout
    title="Productos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Productos', 'href' => route('admin.products.index')],
        ['name' => 'Editar'],
    ]"
>
    @push('css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />    @endpush

     <div class="mb-4">
<form action="{{route('admin.products.dropzone', $product)}}" class="dropzone" id="my-dropzone" method="POST">
@csrf

</form>
            
        </div>

    <x-wire-card>
        <form
            action="{{ route('admin.products.update', $product) }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <x-wire-input
                name="name"
                label="Nombre"
                placeholder="Nombre del producto"
                value="{{ old('name', $product->name) }}"
               
            />

            <x-wire-textarea
                name="description"
                label="Descripción"
                placeholder="Descripción del producto"
            >{{ old('description', $product->description) }}</x-wire-textarea>

            <x-wire-input
                name="price"
                type="number"
                step="0.01"
                label="Precio"
                placeholder="Precio unitario"
                value="{{ old('price', $product->price) }}"
                
            />

            <x-wire-native-select
                label="Categoría"
                name="category_id"
            >
               
                @foreach($categories as $cat)
                    <option
                        value="{{ $cat->id }}"
                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}
                    >
                        {{ $cat->name }}
                    </option>
                @endforeach
            </x-wire-native-select>
            @error('category_id')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror

                   <div class="flex justify-end">
    <x-button type="submit">
        Actualizar
    </x-button>
</div>
        </form>
         
    </x-wire-card>

    @push('js')
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
     
<script>
  // Note that the name "myDropzone" is the camelized
  // id of the form.
  Dropzone.options.myDropzone = {

     addRemoveLinks: true,

    init: function() {
        let myDropzone = this;

        let images = @json($product->images);

        images.forEach(function(image) {
            let mockFile = { 
                id:image.id,
                 name: image.path.split('/').pop(),
                  size: image.size 
                };
            myDropzone.displayExistingFile(mockFile,`{{ Storage::url('${image.path}') }}`);
              myDropzone.emit("complete", mockFile);
              myDropzone.files.push(mockFile);
        });

        this.on("success", function(file, response) {
            // Aquí puedes manejar la respuesta del servidor
            console.log('Imagen subida:', response);
            file.id = response.id; // Asignar el ID de la imagen al archivo
        });

        this.on("removedfile", function(file) {
            
                axios.delete(`/admin/images/${file.id}`)
                    .then(response => {
                        console.log('Imagen eliminada:', response.data);
                    })
                    .catch(error => {
                        console.error('Error al eliminar la imagen:', error);
                    });
            
        });
      
    },
  };
</script>

    @endpush
</x-admin-layout>


