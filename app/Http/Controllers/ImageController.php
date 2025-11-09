<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        \Log::info('Upload začal');
        
        try {
            $request->validate([
                'image' => 'required|image|max:10240', // max 10MB
            ]);
            
            \Log::info('Validace prošla');
            
            if (!$request->hasFile('image')) {
                \Log::error('Žádný soubor nebyl nahrán');
                return back()->with('error', 'Žádný soubor nebyl nahrán');
            }
            
            $file = $request->file('image');
            \Log::info('Soubor:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
            
            $path = $file->store('uploads', 'public');
            \Log::info('Soubor uložen:', ['path' => $path]);
            
            // přesměruje na editor s cestou k obrázku
            return redirect()->route('editor', ['path' => $path]);
            
        } catch (\Exception $e) {
            \Log::error('Chyba při uploadu:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Chyba při nahrávání: ' . $e->getMessage());
        }
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
