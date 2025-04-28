<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UserManagement;

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
    
    // Route for Fabric.js test page
    Route::get('/fabric-test', function () {
        return view('fabric-test');
    })->name('fabric.test');
    
    // Add this new route for the image adjustments test page
    Route::get('/image-adjustments', function () {
        return view('image-adjustments');
    })->name('image-adjustments');
    
    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserManagement::class)->name('users.index');
    });
});
