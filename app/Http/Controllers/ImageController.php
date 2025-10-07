<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        // přesměruje na editor s cestou k obrázku
        return redirect()->route('editor', ['path' => $path]);
    }

    public function createBlank(Request $request)
    {
        $width = $request->input('width', 500);
        $height = $request->input('height', 500);

        // vytvoří prázdný bílý obrázek
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        $filename = 'blank_' . time() . '.png';
        $path = storage_path('app/public/uploads/' . $filename);

        imagepng($image, $path);
        imagedestroy($image);

        return redirect()->route('editor', ['path' => 'uploads/' . $filename]);
    }
}
