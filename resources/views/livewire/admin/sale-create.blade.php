<div x-data="{
products: @entangle('products'),
total: @entangle('total'),
discount_percent: @entangle('discount_percent'),
subtotal: @entangle('subtotal'),
discount_amount: @entangle('discount_amount'),
removeProduct(index){ this.products.splice(index,1); this.recalc(); },
init(){ this.$watch('products', ()=> this.recalc()); this.$watch('discount_percent', ()=> this.recalc()); this.recalc(); },
recalc(){ let sub=0; this.products.forEach(p=> sub += (p.quantity||0)*(p.price||0)); this.subtotal=sub; this.discount_amount= +(sub*(this.discount_percent/100)).toFixed(2); this.total= +(sub-this.discount_amount).toFixed(2); }
}">
    
    <x-wire-card>

        <form wire:submit="save" class="space-y-4">


            <div class="grid lg:grid-cols-4 gap-4">

                <x-wire-native-select label="Tipo de Documento" wire:model="voucher_type">
                    <option value="1">Factura</option>
                    <option value="2">Boleta</option>
                </x-wire-native-select>

                
                
                <div class="grid grid-cols-2 gap-2">

                <x-wire-input label="Serie" wire:model="serie" placeholder="Serie" disabled/>

                <x-wire-input label="Correlativo" wire:model="correlative" placeholder="Correlativo" disabled />

                
                </div>
                
                <x-wire-input label="Fecha" wire:model="date" type="date" />

                 <x-wire-select 
                 label="Cotización" 
                 wire:model.live="quote_id" 
                 placeholder="Seleccione una cotización"
                 :async-data="[
                    'api' => route('api.sales.index'),
                    'method' => 'GET',
                ]" 
                option-label="name" 
                option-value="id"
                option-description="description"
                />

                <div class="col-span-2">
                      <x-wire-select label="Cliente" wire:model="customer_id" placeholder="Seleccione un cliente"
                :async-data="[
                    'api' => route('api.customers.index'),
                    'method' => 'GET',
                ]" option-label="name" option-value="id"
                />
                </div>

                                <div class="col-span-2">
                                            @php $allServices = collect($products)->count() > 0 && collect($products)->every(fn($p)=> ($p['is_service'] ?? false)); @endphp
                                            <x-wire-select label="Almacén{{ $allServices ? ' (solo servicios - opcional)' : '' }}" wire:model="warehouse_id" placeholder="Seleccione un almacén"
                :async-data="[
                    'api' => route('api.warehouses.index'),
                    'method' => 'GET',
                ]" option-label="name" option-value="id"
                option-description="description"
                />
                                        @if($allServices)
                                                <p class="mt-1 text-xs text-gray-500">Todos los ítems son servicios: puedes dejar el almacén en blanco.</p>
                                        @endif
                </div>

                





            </div>

          

            <div class="lg:flex lg:space-x-4">

                <x-wire-select 
                label="Productos"
                 wire:model="product_id" 
                 placeholder="Seleccione un producto"
                    :async-data="[
                        'api' => route('api.products.index'),
                        'method' => 'GET',
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

<div class="overflow-x-auto w-full hidden md:block">
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


                     <template x-for="(product, index) in products" :key="'desktop-'+product.id">
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

            <!-- Mobile list -->
            <div class="md:hidden space-y-3">
                <template x-for="(product, index) in products" :key="'mobile-'+product.id">
                    <div class="border rounded-lg bg-white p-3 shadow-sm">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-medium text-sm" x-text="product.name"></p>
                                <span class="mt-1 inline-block text-[10px] px-1 rounded bg-gray-200" x-show="product.is_service">Servicio</span>
                            </div>
                            <button type="button" x-on:click="removeProduct(index)" class="text-red-500 text-xs font-semibold">Eliminar</button>
                        </div>
                        <template x-if="product.description !== undefined">
                            <div class="mt-2">
                                <label class="text-[10px] font-semibold text-gray-500 tracking-wide">Descripción</label>
                                <input type="text" x-model="product.description" class="mt-1 w-full border-gray-300 rounded text-xs focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </template>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 tracking-wide">Cant.</label>
                                <input type="number" inputmode="decimal" x-model="product.quantity" class="mt-1 w-full border-gray-300 rounded text-xs text-center focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 tracking-wide">Precio</label>
                                <input type="number" step="0.01" inputmode="decimal" x-model="product.price" class="mt-1 w-full border-gray-300 rounded text-xs text-center focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div class="mt-2 text-right text-[11px] text-gray-600">
                            Subtotal: Q <span x-text="(product.quantity * product.price).toFixed(2)"></span>
                        </div>
                    </div>
                </template>
                <template x-if="products.length===0">
                    <p class="text-center text-gray-500 text-sm">Sin productos agregados.</p>
                </template>
            </div>

                   <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <x-wire-input label="Descuento (%)" type="number" step="0.01" wire:model="discount_percent" x-model="discount_percent" />
                            <div class="bg-gray-50 rounded p-3 text-sm space-y-1">
                                <div class="flex justify-between"><span>Subtotal:</span><span>Q <span x-text="subtotal.toFixed(2)"></span></span></div>
                                <div class="flex justify-between"><span>Descuento:</span><span>- Q <span x-text="discount_amount.toFixed(2)"></span></span></div>
                                <div class="flex justify-between font-semibold border-t pt-1"><span>Total:</span><span>Q <span x-text="total.toFixed(2)"></span></span></div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <x-label>Observaciones</x-label>
                            <x-wire-input wire:model="observation" placeholder="Ingrese observaciones" />
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
