
{{-- Mostrar Stock de productos por almacén
//resources/views/admin/products/stock.blade.php --}}

<button wire:click="showStock({{ $product->id }})" >
    
 {{$stock}}   
</button>
