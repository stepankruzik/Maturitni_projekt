<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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


}
