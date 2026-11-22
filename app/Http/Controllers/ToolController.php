<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function show(Request $request, string $tool = 'editor'): View
    {
        $tools = config('tools', []);

        abort_unless(isset($tools[$tool]), 404);

        $toolConfig = $tools[$tool];
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $toolConfig['name'],
            'description' => $toolConfig['description'],
            'applicationCategory' => 'MultimediaApplication',
            'operatingSystem' => 'Web',
            'url' => route('tool.show', ['tool' => $tool]),
        ];

        $path = $request->query('path');

        if ($path || $tool === 'editor') {
            return view('editor', [
                'imagePath' => $path ? asset($path) : null,
                'tool' => $tool,
                'toolConfig' => $toolConfig,
                'structuredData' => $structuredData,
            ]);
        }

        return view('index', [
            'tool' => $tool,
            'toolConfig' => $toolConfig,
            'structuredData' => $structuredData,
        ]);
    }
}
