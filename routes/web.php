<?php

use App\Http\Controllers\Institucionescontroller;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route :: get('/view_entidad_publica/crear', [Institucionescontroller::class, 'crear'])->name('view_entidad_publica.crear');
Route :: post('/view_entidad_publica/store', [Institucionescontroller::class, 'store'])->name('view_entidad_publica.store');
Route :: get('/view_entidad_publica/leer', [Institucionescontroller::class, 'leer'])->name('view_entidad_publica.leer');
Route :: put('/view_entidad_publica/{entidadpublica}', [Institucionescontroller::class, 'update'])->name('view_entidad_publica.update');
Route ::get ('/login', function () {
    return view('layouts.view_login.login');
})->name('login');
Route ::get ('/', function () {
    return view('welcome');
})->name('inicio');
