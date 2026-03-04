<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/openapi.json', function () {
    return response()->file(public_path('docs/openapi.json'), [
        'Content-Type' => 'application/json',
    ]);
})->name('docs.openapi');

Route::get('/swagger', function () {
    return view('swagger');
})->name('swagger');
