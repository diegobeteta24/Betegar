<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product; // usamos misma tabla
use App\Models\Category;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index');
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'price' => ['required','numeric','min:0'],
            'category_id' => ['required','exists:categories,id'],
        ]);
        $data['type'] = 'service';
        // stock no aplica
        unset($data['stock']);
        $service = Product::create($data);

        return redirect()->route('admin.services.index')->with('sweet-alert', [
            'icon' => 'success',
            'title' => '¡Servicio creado!',
            'timer' => 2500,
            'showConfirmButton' => false,
        ]);
    }

    public function edit(Product $service)
    {
        abort_unless($service->type === 'service', 404);
        $categories = Category::all();
        return view('admin.services.edit', compact('service','categories'));
    }

    public function update(Request $request, Product $service)
    {
        abort_unless($service->type === 'service', 404);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'price' => ['required','numeric','min:0'],
            'category_id' => ['required','exists:categories,id'],
        ]);
        $service->update($data);
        return redirect()->route('admin.services.edit', $service)->with('sweet-alert', [
            'icon' => 'success',
            'title' => '¡Servicio actualizado!',
            'timer' => 2500,
            'showConfirmButton' => false,
        ]);
    }

    public function destroy(Product $service)
    {
        abort_unless($service->type === 'service', 404);
        $service->delete();
        return redirect()->route('admin.services.index')->with('sweet-alert', [
            'icon' => 'success',
            'title' => '¡Servicio eliminado!',
            'timer' => 2500,
            'showConfirmButton' => false,
        ]);
    }

    public function import()
    {
        return view('admin.services.import');
    }
}
