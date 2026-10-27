<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas — Portal Ciudadano
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('public.home'))->name('home');
Route::get('/tramites',    fn() => view('public.home'))->name('tramites.index');
Route::get('/expedientes', fn() => view('public.home'))->name('expedientes.index');
Route::get('/citas',       fn() => view('public.home'))->name('citas.index');
Route::get('/ayuda',       fn() => view('public.home'))->name('ayuda.index');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (placeholder)
|--------------------------------------------------------------------------
*/
Route::get('/login',    fn() => view('public.home'))->name('login');
Route::get('/register', fn() => view('public.home'))->name('register');
Route::post('/logout',  fn() => redirect('/'))->name('logout');
Route::get('/profile',  fn() => view('public.home'))->name('profile.show');

/*
|--------------------------------------------------------------------------
| Rutas del Portal Interno — Enterprise (privado)
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard',    fn() => view('portal.dashboard'))->name('dashboard');
    Route::get('/mesa-dictamen',fn() => view('portal.dashboard'))->name('mesa-dictamen');
    Route::get('/expedientes',  fn() => view('portal.dashboard'))->name('expedientes');
    Route::get('/citas',        fn() => view('portal.dashboard'))->name('citas');
    Route::get('/matriz-cargas',fn() => view('portal.dashboard'))->name('matriz-cargas');
    Route::get('/metricas',     fn() => view('portal.dashboard'))->name('metricas');
    Route::get('/usuarios',     fn() => view('portal.dashboard'))->name('usuarios');
    Route::get('/parametros',   fn() => view('portal.dashboard'))->name('parametros');
    Route::get('/auditoria',    fn() => view('portal.dashboard'))->name('auditoria');
});
