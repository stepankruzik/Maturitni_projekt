<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;


Route::get('/', [ProjectController::class, 'index'])->name('home');
Route::post('/upload', [ImageController::class, 'upload'])->name('upload');
Route::post('/createBlank', [ImageController::class, 'createBlank'])->name('createBlank');

Route::get('/editor', function() {
    $imagePath = 'storage/' . request()->get('path');
    return view('editor', compact('imagePath'));
})->name('editor');
