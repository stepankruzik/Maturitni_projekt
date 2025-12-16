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
        'image' => [
            'required',
            'image',
            'max:10240', // Max. 10MB
            'dimensions:max_width=3840,max_height=2160', // Max. 4K
        ],
    ]);

    $file = $request->file('image');
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    $file->move(public_path('uploads'), $filename);

    return redirect()->route('editor', ['path' => 'uploads/' . $filename]);
}


   public function createBlank(Request $request)
{

    $maxWidth = 3840;
    $maxHeight = 2160;

    $width = min((int)$request->input('width', 500), $maxWidth);
    $height = min((int)$request->input('height', 500), $maxHeight);

    if ($request->input('width') > $maxWidth || $request->input('height') > $maxHeight) {
        return redirect()->back()->withErrors(['message' => 'Maximální velikost plátna je 3840x2160 px (4K). Zvolené rozměry byly automaticky sníženy.']);
    }

    $image = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);

    $filename = 'blank_' . time() . '.png';
    $path = public_path('uploads/' . $filename);

    imagepng($image, $path);
    imagedestroy($image);

    return redirect()->route('editor', ['path' => 'uploads/' . $filename]);
}

//Ukládání obrázku z editoru na server
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
