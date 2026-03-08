<?php

use Illuminate\Http\UploadedFile;

test('editor page renders canvas and filter section', function () {
    $response = $this->get(route('editor', ['path' => 'uploads/test.png']));

    $response->assertOk();
    $response->assertSee('id="canvasWorkspace"', false);
    $response->assertSee('id="canvas"', false);
    $response->assertSee('id="drawFloatingToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm border border-gray-200 rounded-xl shadow-xl px-3 py-2.5 z-50 flex gap-3 items-center text-slate-900"', false);
    $response->assertSee('id="textToolbar" class="hidden fixed bg-white/95 backdrop-blur-sm shadow-xl rounded-xl px-3 py-2 flex items-center gap-2 z-50 border border-gray-200 text-slate-900"', false);
    $response->assertSee('id="textContextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px] text-slate-900"', false);
    $response->assertSee('id="contextMenu" class="hidden absolute bg-white border border-gray-300 rounded-lg shadow-lg py-1 z-50 min-w-[150px] text-slate-900"', false);
    $response->assertSee('Filtry');
    $response->assertSee('Ruční vyladění');
    $response->assertSee('data-target="panelFilters"', false);
    $response->assertSee('id="brightness"', false);
    $response->assertSee('function createImageRotateCornerControl(', false);
    $response->assertSee('function applyImageTransformControls(imageObj)', false);
    $response->assertSee('const FABRIC_FILTER_TEXTURE_SIZE = 4096;', false);
    $response->assertSee('fabric.textureSize = FABRIC_FILTER_TEXTURE_SIZE;', false);
    $response->assertSee('function createFilterPreviewSource(sourceEl, width, height) {', false);
    $response->assertSee('const basePreviewSource = createFilterPreviewSource(sourceEl, previewWidth, previewHeight);', false);
    $response->assertSee('Maximum je ${MAX_WIDTH}×${MAX_HEIGHT} v libovolné orientaci.', false);
    $response->assertSee('HEIC/HEIF tato verze editoru zatím nepodporuje.', false);
    $response->assertDontSee('data-target="panelLevels"', false);
    $response->assertSee(json_encode(asset('uploads/test.png')), false);
});

test('home page uses updated upload dimension limit', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('const APP_MAX_FILE_SIZE = 50 * 1024 * 1024;', false);
    $response->assertSee('const SERVER_MAX_FILE_SIZE =', false);
    $response->assertSee('const MAX_WIDTH = 4032;', false);
    $response->assertSee('const MAX_HEIGHT = 3024;', false);
    $response->assertSee('function isHeicFile(file)', false);
    $response->assertSee('const longSide = Math.max(img.width, img.height);', false);
    $response->assertSee('const shortSide = Math.min(img.width, img.height);', false);
    $response->assertSee('Soubor je příliš velký pro aktuální nastavení serveru', false);
});

test('upload accepts 4032x3024 image in both orientations', function () {
    foreach ([[4032, 3024], [3024, 4032]] as [$width, $height]) {
        $file = UploadedFile::fake()->image("photo-{$width}x{$height}.jpg", $width, $height);

        $response = $this->post(route('upload'), ['image' => $file]);

        $response->assertRedirect();
        expect($response->headers->get('location'))->toContain('/editor?path=uploads%2F');
    }
});

test('upload rejects heic with clear czech message', function () {
    $file = UploadedFile::fake()->create('photo.heic', 100, 'image/heic');

    $response = $this->from(route('home'))->post(route('upload'), ['image' => $file]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHasErrors([
        'image' => 'Povolené formáty jsou JPG, PNG, GIF, BMP a WebP. HEIC/HEIF tato verze editoru zatím nepodporuje.',
    ]);
});