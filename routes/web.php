<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Route for composite editor
    Route::get('/composite-editor/{id}', function ($id) {
        return view('composite-editor', ['compositeId' => $id]);
    })->name('composite.editor');
});
