<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProjectController extends Controller
{
    public function index() 
    {
        return view('index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

    $file = $request->file('image');
    $filename = $file->getClientOriginalName(); 
    $file->storeAs('public/uploads', $filename);

    return redirect()->route('editor', $filename);
}


   public function createBlank(Request $request)
{
    $request->validate([
        'width' => 'required|integer|min:50|max:5000',
        'height' => 'required|integer|min:50|max:5000',
    ]);

    $width = $request->input('width');
    $height = $request->input('height');

    $image = Image::canvas($width, $height, '#ffffff');
    $filename = 'blank.png'; 
    $path = storage_path('app/public/uploads/' . $filename);
    $image->save($path);

    return redirect()->route('editor', $filename);
}


public function editor($filename)
{
    $path = 'storage/uploads/' . $filename;

    return view('editor', [
        'imagePath' => asset($path)
    ]);
}


}
