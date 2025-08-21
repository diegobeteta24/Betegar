<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ImageController extends Controller
{
   
   public function destroy(Image $image)
    {
        Gate::authorize('image.delete', $image);
        // Eliminar físicamente
        Storage::delete($image->path);
        // Eliminar registro
        $image->delete();

        return response()->json(['deleted' => true]);
    }
}
