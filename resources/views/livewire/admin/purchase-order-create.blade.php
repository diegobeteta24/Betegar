<div x-data="{

products: @entangle('products'),

total: @entangle('total'),

removeProduct(index) {
    this.products.splice(index, 1);
},

init()
{
this.$watch('products', (newProducts) => {
   let total = 0;
    newProducts.forEach(product => {
        total += product.quantity * product.price;
    });

    this.total = total;

});
 
}

}">
    
    <x-wire-card>

        <form wire:submit="save" class="space-y-4">


            <div class="grid lg:grid-cols-4 gap-4">

                <x-wire-native-select label="Tipo de Documento" wire:model="voucher_type">
                    <option value="1">Factura</option>
                    <option value="2">Boleta</option>
                </x-wire-native-select>

                <x-wire-input label="Serie" wire:model="serie" placeholder="Serie" disabled />

                <x-wire-input label="Correlativo" wire:model="correlative" placeholder="Correlativo" disabled />

                <x-wire-input label="Fecha" wire:model="date" type="date" />





            </div>

            <x-wire-select label="Proveedor" wire:model="supplier_id" placeholder="Seleccione un proveedor"
                :async-data="[
                    'api' => route('api.suppliers.index'),
                    'method' => 'POST',
                ]" option-label="name" option-value="id"
                
                />

            <div class="lg:flex lg:space-x-4">

                <x-wire-select 
                label="Productos"
                 wire:model="product_id" 
                 placeholder="Seleccione un producto"
                    :async-data="[
                        'api' => route('api.products.index'),
                        'method' => 'POST',
                    ]" option-label="name" option-value="id" 
                    class="flex-1" 
                    />
                <div class="flex-shrink-0">
                    <x-wire-button 
                    wire:click="addProduct" 
                    spinner="addProduct"
                    class="w-full mt-4 lg:mt-6.5">
                        Agregar Producto
                    </x-wire-button>

                </div>


            </div>

<div class="overflow-x-auto w-full">
            <table class="w-full text-sm text-left">

                <thead>

                    <tr class="text-gray-700 border-y bg-blue-50">
                        <th class="py-2 px-4">
                            Producto
                        </th>
                        <th class="py-2 px-4">
                            Cantidad
                        </th>
                        <th class="py-2 px-4">
                            Precio Unitario
                        </th>
                        <th class="py-2 px-4">
                            Subtotal

                        </th>
                        <th class="py-2 px-4">

                        </th>

                    </tr>

                </thead>

                <tbody>


                     <template x-for="(product, index) in products" :key="product.id">
                       <tr class="border-b">
                           <td class="py-1 px-4" x-text="product.name">

                           </td>
                           <td class="py-1 px-4">
                            <x-wire-input
                                type="number"
                                x-model="product.quantity"
                                class="w-20"
                               
                            />
                            </td>
                            <td class="py-1 px-4">
                            <x-wire-input
                                type="number"
                                x-model="product.price"
                                class="w-20"
                                step="0.01"
                               />
                            </td>
                            <td class="py-1 px-4"
                                x-text="(product.quantity * product.price).toFixed(2)">
                            </td>
                            <td class="py-1 px-4">
                                <x-wire-mini-button 
                                rounded 
                                x-on:click="removeProduct(index)"
                                icon="trash"
                                red
                                />
                               
                                    
                               
                            </td>
                        </tr>
                    </template>

                    <template x-if="products.length === 0">
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">
                                No hay productos agregados.
                            </td>
                        </tr>

                        
                    </template>
                   
                   </tbody>



                   </table>
                     </div>

                   <div class="flex items-center space-x-4">

                    <x-label>
                        Observaciones
                    </x-label>
                    <x-wire-input wire:model="observation" placeholder="Ingrese observaciones"
                        class="flex-1" />

                        <div>
                            Total: Q/. <span x-text="total.toFixed(2)"></span> 
                        </div>

                       

                   </div>
                   <div class="flex justify-end">
                       <x-wire-button
                        type="submit" 
                        icon="check"
                        spinner="save">
                           Guardar
                       </x-wire-button>
                   </div>

        </form>


    </x-wire-card>
</div>
