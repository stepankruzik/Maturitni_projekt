<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('/', [ProjectController::class, 'index'])->name('home');
Route::post('/upload', [ProjectController::class, 'upload'])->name('upload');
Route::post('/create-blank', [ProjectController::class, 'createBlank'])->name('createBlank');

Route::get('/editor/{filename}', [ProjectController::class, 'editor'])->name('editor');