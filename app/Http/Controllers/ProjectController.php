<?php

namespace App\Http\Controllers;

class ProjectController extends Controller
{
    public function index()
    {
        $tool = 'editor';
        $toolConfig = config('tools.editor');
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => $toolConfig['name'],
            'description' => $toolConfig['description'],
            'applicationCategory' => 'MultimediaApplication',
            'operatingSystem' => 'Web',
            'url' => route('tool.show', ['tool' => $tool]),
        ];

        return view('index', compact('tool', 'toolConfig', 'structuredData'));
    }
}
