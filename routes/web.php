<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ToolController;

Route::get('/', [ProjectController::class, 'index'])->name('home');
Route::post('/upload', [ImageController::class, 'upload'])->name('upload');

Route::get('/editor', [ToolController::class, 'show'])->name('editor');
Route::get('/{tool}', [ToolController::class, 'show'])
    ->whereIn('tool', array_keys(config('tools')))
    ->name('tool.show');
Route::post('/save-image', [ImageController::class, 'store']);
