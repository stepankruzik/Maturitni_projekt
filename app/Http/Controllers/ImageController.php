<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ImageController extends Controller
{
public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|mimes:jpeg,jpg,png,gif,bmp,webp,heic,heif|max:51200|dimensions:max_width=4000,max_height=2250', // max 50MB, max rozměry 4000x2250 (4K), včetně HEIC
    ]);

    $file = $request->file('image');
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // uložíme rovnou do public/uploads
    $file->move(public_path('uploads'), $filename);

    return redirect()->route('editor', ['path' => 'uploads/' . $filename]);
}


   public function createBlank(Request $request)
{
    $width = $request->input('width', 500);
    $height = $request->input('height', 500);

    $image = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);

    $filename = 'blank_' . time() . '.png';
    $path = public_path('uploads/' . $filename);

    imagepng($image, $path);
    imagedestroy($image);

    return redirect()->route('editor', ['path' => 'uploads/' . $filename]);
}

public function store(Request $request)
{
    $request->validate([
        'image' => 'required|string', // Base64 řetězec
        'format' => 'required|string',
    ]);

    $image = $request->image;
    $format = $request->get('format', 'png');

    // Odstranění base64 hlavičky (např. data:image/png;base64,)
    $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
    $image = base64_decode($image);

    $filename = 'editor_' . Str::random(10) . '.' . $format;
    
    // Cesta do veřejné složky 'public/uploads'
    $path = public_path('uploads/' . $filename); 

    // Uložení souboru
    if (file_put_contents($path, $image) === false) {
        return response()->json([
            'success' => false,
            'message' => 'Chyba při ukládání souboru.'
        ], 500);
    }

    // Návrat cesty přístupné z webu
    return response()->json([
        'success' => true,
        'path' => 'uploads/' . $filename // Relativní cesta z public/
    ]);
}
}
