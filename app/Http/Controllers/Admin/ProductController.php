<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        // Pasamos las categorías para el <select>
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        // 1. Validar datos
        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'description'  => ['nullable','string','max:1000'],
            'price'        => ['required','numeric','min:0'],
            'category_id'  => ['required','exists:categories,id'],
        ]);

        // 2. Crear el producto
        $product = Product::create($data);

        // 3. Redirigir a edit con SweetAlert
        return redirect()
            ->route('admin.products.index', $product)
            ->with('sweet-alert', [
                'icon'    => 'success',
                'title'   => '¡Producto creado!',
                'text'    => 'Ya puedes completar más detalles.',
                'timer'   => 3000,
                'showConfirmButton' => false,
            ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
     public function edit(Product $product)
    {
        // Carga las categorías para el select
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }
    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Product $product)
    {
        // 1. Validar datos
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'price'       => ['required','numeric','min:0'],
            'category_id' => ['required','exists:categories,id'],
        ]);

        // 2. Actualizar
        $product->update($data);

        // 3. Redirigir con SweetAlert
        return redirect()
            ->route('admin.products.edit', $product)
            ->with('sweet-alert', [
                'icon'    => 'success',
                'title'   => '¡Producto actualizado!',
                'text'    => 'Los cambios se guardaron correctamente.',
                'timer'   => 3000,
                'showConfirmButton' => false,
            ]);
    }
    /**
     * Remove the specified resource from storage.
     */
public function destroy(Product $product)
{
    // 1. Verificar relaciones antes de eliminar
    $relations = [
        'inventarios'       => $product->inventories()->exists(),
        'órdenes de compra' => $product->purchaseOrders()->exists(),
        'cotizaciones'      => $product->quotes()->exists(),
        // 'imágenes'          => $product->images()->exists(),
    ];

    // Filtrar sólo las que tengan datos
    $blocked = array_keys(array_filter($relations));

    if (count($blocked)) {
        // Construir texto dinámico
        $list = implode(', ', $blocked);
        return redirect()
            ->route('admin.products.index')
            ->with('sweet-alert', [
                'icon'    => 'error',
                'title'   => '¡No se puede eliminar!',
                'text'    => "Este producto tiene registros en: {$list}.",
                'showConfirmButton' => true,
            ]);
    }

    // 2. Si no tiene ninguna relación, eliminarlo
    $product->delete();

    // 3. Redirigir con alerta de éxito
    return redirect()
        ->route('admin.products.index')
        ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Producto eliminado!',
            'text'    => 'El producto ha sido eliminado correctamente.',
            'timer'   => 3000,
            'showConfirmButton' => false,
        ]);
    }

     public function dropzone(Request $request, Product $product)
    {
       
   
           $image= $product->images()->create([
            'path' =>  Storage::put('/images', $request->file('file')),
            'size' => $request->file('file')->getSize(),
        ]);

       
        return response()->json([
            'id' => $image->id,
            'path' => $image->path,
            
        ]);
  
    }

    public function kardex(Product $product){

        return view('admin.products.kardex', compact('product'));
    }

    public function import(Request $request)
    {
       return view('admin.products.import');
    }
}
