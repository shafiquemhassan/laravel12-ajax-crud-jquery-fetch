<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/notesjQuery', fn() => view('notes.jQuery'));
Route::get('/notesfetch', fn() => view('notes.fetch'));