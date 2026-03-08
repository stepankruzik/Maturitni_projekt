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
            'file',
            'mimes:jpeg,jpg,png,gif,bmp,webp',
            'max:51200',
            function ($attribute, $value, $fail) {
                $size = @getimagesize($value->getPathname());

                if (!$size) {
                    $fail('Nelze načíst rozlišení obrázku. Zkuste jiný soubor.');
                    return;
                }

                [$width, $height] = $size;
                $longSide = max($width, $height);
                $shortSide = min($width, $height);

                if ($longSide > 4032 || $shortSide > 3024) {
                    $fail('Rozlišení obrázku je příliš velké. Maximum je 4032×3024 v libovolné orientaci.');
                }
            },
        ], // max 50MB, běžná mobilní fotka 12 Mpx, včetně HEIC
    ], [
        'image.required' => 'Vyber obrázek, který chceš nahrát.',
        'image.file' => 'Vybraný soubor se nepodařilo přečíst jako soubor.',
        'image.uploaded' => 'Soubor se nepodařilo nahrát na server. Aktuální PHP limit uploadu je ' . ini_get('upload_max_filesize') . '.',
        'image.mimes' => 'Povolené formáty jsou JPG, PNG, GIF, BMP a WebP. HEIC/HEIF tato verze editoru zatím nepodporuje.',
        'image.max' => 'Soubor je příliš velký. Maximum aplikace je 50 MB.',
    ]);

    $file = $request->file('image');
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // uložíme rovnou do public/uploads
    $file->move(public_path('uploads'), $filename);

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
