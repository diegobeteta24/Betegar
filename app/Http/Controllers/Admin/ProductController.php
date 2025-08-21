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

    $data['type'] = 'product';

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

    $data['type'] = 'product';

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
    // Soft delete (papelera). Permitimos aunque tenga relaciones para preservar integridad.
    $product->delete();

    return redirect()
        ->route('admin.products.index')
        ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Producto enviado a papelera!',
            'text'    => 'Puedes restaurarlo o eliminarlo definitivamente luego.',
            'timer'   => 3000,
            'showConfirmButton' => false,
        ]);
}

    /**
     * Restaurar un producto eliminado (soft delete)
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if (!$product->trashed()) {
            return redirect()->route('admin.products.index')->with('sweet-alert', [
                'icon' => 'info',
                'title' => 'El producto ya estaba activo',
                'timer' => 2500,
                'showConfirmButton' => false,
            ]);
        }

        $product->restore();

        return redirect()->route('admin.products.index')->with('sweet-alert', [
            'icon' => 'success',
            'title' => '¡Producto restaurado!',
            'timer' => 2500,
            'showConfirmButton' => false,
        ]);
    }

    /**
     * Eliminación definitiva (force delete)
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if (!$product->trashed()) {
            return redirect()->route('admin.products.index')->with('sweet-alert', [
                'icon' => 'error',
                'title' => 'Acción inválida',
                'text' => 'El producto no está en papelera.',
                'showConfirmButton' => true,
            ]);
        }

        // Verificar relaciones antes de borrar definitivamente
        $relations = [
            'inventarios'       => method_exists($product, 'inventories') ? $product->inventories()->exists() : false,
            'órdenes de compra' => method_exists($product, 'purchaseOrders') ? $product->purchaseOrders()->exists() : false,
            'cotizaciones'      => method_exists($product, 'quotes') ? $product->quotes()->exists() : false,
        ];
        $blocked = array_keys(array_filter($relations));
        if (count($blocked)) {
            $list = implode(', ', $blocked);
            return redirect()->route('admin.products.index')->with('sweet-alert', [
                'icon' => 'error',
                'title' => 'No se puede eliminar definitivamente',
                'text' => "Existen registros en: {$list}.",
                'showConfirmButton' => true,
            ]);
        }

        // Borrar archivos asociados (imágenes) antes de force delete
        if (method_exists($product, 'images')) {
            foreach ($product->images as $img) {
                if ($img->path) {
                    \Illuminate\Support\Facades\Storage::delete($img->path);
                }
                $img->delete();
            }
        }

        $product->forceDelete();

        return redirect()->route('admin.products.index')->with('sweet-alert', [
            'icon' => 'success',
            'title' => '¡Producto eliminado definitivamente!',
            'timer' => 2500,
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
