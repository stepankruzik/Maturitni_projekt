<?php

test('editor page renders canvas and filter section', function () {
    $response = $this->get(route('editor', ['path' => 'uploads/test.png']));

    $response->assertOk();
    $response->assertSee('id="canvasWorkspace"', false);
    $response->assertSee('id="canvas"', false);
    $response->assertSee('Filtry');
    $response->assertSee('data-target="panelFilters"', false);
    $response->assertSee(json_encode(asset('uploads/test.png')), false);
});