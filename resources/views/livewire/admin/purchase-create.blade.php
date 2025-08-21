<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    removeProduct(index) {
        this.products.splice(index, 1);
    },
    init() {
        this.$watch('products', (newProducts) => {
            let sum = 0;
            newProducts.forEach(p => sum += p.quantity * p.price);
            this.total = sum;
        });
    }
}">
  <x-wire-card>
    <form wire:submit="save" class="space-y-4 p-4">

      <!-- GRID: 1 columna por defecto, 4 en lg+ -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <x-wire-native-select
          label="Tipo de Documento"
          wire:model="voucher_type"
          class="w-full"
        >
          <option value="1">Factura</option>
          <option value="2">Boleta</option>
        </x-wire-native-select>

        <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
          <x-wire-input
            label="Serie"
            wire:model="serie"
            placeholder="Serie"
            class="w-full"
          />
          <x-wire-input
            label="Correlativo"
            wire:model="correlative"
            placeholder="Correlativo"
            class="w-full"
          />
        </div>

        <x-wire-input
          label="Fecha"
          wire:model="date"
          type="date"
          class="w-full"
        />

        <x-wire-select
          label="Orden de Compra"
          wire:model.live="purchase_order_id"
          placeholder="Seleccione una orden de compra"
          :async-data="[
            'api'    => route('api.purchase-orders.index'),
            'method' => 'POST',
          ]"
          option-label="name"
          option-value="id"
          option-description="description"
          class="w-full"
        />

        <div class="col-span-1 lg:col-span-2">
          <x-wire-select
            label="Proveedor"
            wire:model="supplier_id"
            placeholder="Seleccione un proveedor"
            :async-data="[
              'api'    => route('api.suppliers.index'),
              'method' => 'POST',
            ]"
            option-label="name"
            option-value="id"
            class="w-full"
          />
        </div>

        <div class="col-span-1 lg:col-span-2">
          <x-wire-select
            label="Almacén"
            wire:model="warehouse_id"
            placeholder="Seleccione un almacén"
            :async-data="[
              'api'    => route('api.warehouses.index'),
              'method' => 'POST',
            ]"
            option-label="name"
            option-value="id"
            option-description="description"
            class="w-full"
          />
        </div>

        <div class="col-span-1 lg:col-span-2">
          <x-wire-select
            label="Cuenta Bancaria (opcional)"
            wire:model="bank_account_id"
            placeholder="Seleccione una cuenta bancaria"
            :async-data="[
              'api'    => route('api.bank-accounts.index'),
              'method' => 'POST',
            ]"
            option-label="name"
            option-value="id"
            class="w-full"
            hint="Si seleccionas una cuenta se generará un débito automático"
          />
        </div>
      </div>

      <!-- FLEX: columna en sm, fila en lg -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:space-x-4 lg:space-y-0">
        <x-wire-select
          label="Productos"
          wire:model="product_id"
          placeholder="Seleccione un producto"
          :async-data="[
            'api'    => route('api.products.index'),
            'method' => 'POST',
          ]"
          option-label="name"
          option-value="id"
          class="w-full lg:flex-1"
        />
        <div class="w-full lg:w-auto">
          <x-wire-button
            wire:click="addProduct"
            spinner="addProduct"
            class="w-full mt-4 lg:mt-6.5"
          >
            Agregar Producto
          </x-wire-button>
        </div>
      </div>

      <!-- Tabla con scroll horizontal -->
      <div class="overflow-x-auto w-full">
        <table class="w-full text-sm text-left">
          <thead>
            <tr class="text-gray-700 border-y bg-blue-50">
              <th class="py-2 px-4">Producto</th>
              <th class="py-2 px-4">Cantidad</th>
              <th class="py-2 px-4">Precio Unitario</th>
              <th class="py-2 px-4">Subtotal</th>
              <th class="py-2 px-4"></th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(product, index) in products" :key="product.id">
              <tr class="border-b">
                <td class="py-1 px-4" x-text="product.name"></td>
                <td class="py-1 px-4">
                  <x-wire-input type="number" x-model="product.quantity" class="w-20"/>
                </td>
                <td class="py-1 px-4">
                  <x-wire-input type="number" x-model="product.price" class="w-20" step="0.01"/>
                </td>
                <td class="py-1 px-4" x-text="(product.quantity * product.price).toFixed(2)"></td>
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

      <!-- Observaciones + Total -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:space-x-4 lg:space-y-0">
        <x-label>Observaciones</x-label>
        <x-wire-input
          wire:model="observation"
          placeholder="Ingrese observaciones"
          class="w-full lg:flex-1"
        />
        <div>Total: Q/. <span x-text="total.toFixed(2)"></span></div>
      </div>

      <!-- Botón Guardar -->
      <div class="flex justify-end">
        <x-wire-button type="submit" icon="check" spinner="save">
          Guardar
        </x-wire-button>
      </div>
    </form>
  </x-wire-card>
</div>
