<?php

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
    $response->assertDontSee('data-target="panelLevels"', false);
    $response->assertSee(json_encode(asset('uploads/test.png')), false);
});