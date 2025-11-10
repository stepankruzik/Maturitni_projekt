<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|max:10240',
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

}
