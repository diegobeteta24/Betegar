<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
   
   public function destroy(Image $image)
    {
        // Eliminar físicamente
        Storage::delete($image->path);
        // Eliminar registro
        $image->delete();

        return response()->json(['deleted' => true]);
    }
}
