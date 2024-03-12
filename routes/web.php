<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SincronizacionSiatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::middleware('auth')->group(function(){
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    Route::prefix('/empresa')->group(function(){
        Route::get('/listado', [EmpresaController::class, 'listado']);
        Route::post('/guarda', [EmpresaController::class, 'guarda']);
        Route::post('/ajaxListado', [EmpresaController::class, 'ajaxListado']);
    });

    Route::prefix('/sincronizacion')->group(function(){
        Route::get('/listado', [SincronizacionSiatController::class, 'listado']);
        Route::post('/ajaxListadoTipoDocumentoSector', [SincronizacionSiatController::class, 'ajaxListadoTipoDocumentoSector']);
        Route::post('/sincronizarTipoDocumentoSector', [SincronizacionSiatController::class, 'sincronizarTipoDocumentoSector']);
        // Route::post('/guarda', [EmpresaController::class, 'guarda']);
    });

});
