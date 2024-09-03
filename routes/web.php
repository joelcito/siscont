<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EventoSignificativoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RegistroCompraController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SincronizacionSiatController;
use App\Http\Controllers\SuscripcionController;
use App\Models\RegistroCompra;
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
    // return view('welcome');
    // return view('welcome');
    // return view('home.inicio');
    return redirect('home');

});

Auth::routes();


// Route::middleware('auth')->group(function(){

Route::middleware(['auth', 'one.session'])->group(function () {
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    Route::prefix('/empresa')->group(function(){
        Route::get('/listado', [EmpresaController::class, 'listado']);
        Route::post('/guarda', [EmpresaController::class, 'guarda']);
        Route::get('/detalle/{empresa_id}', [EmpresaController::class, 'detalle']);
        Route::post('/ajaxListado', [EmpresaController::class, 'ajaxListado']);

        Route::post('/ajaxListadoSucursal', [EmpresaController::class, 'ajaxListadoSucursal']);
        Route::post('/guardaSucursal', [EmpresaController::class, 'guardaSucursal']);

        Route::post('/ajaxListadoPuntoVenta', [EmpresaController::class, 'ajaxListadoPuntoVenta']);
        Route::post('/guardaPuntoVenta', [EmpresaController::class, 'guardaPuntoVenta']);
        Route::post('/ajaxRecuperarPuntosVentasSelect', [EmpresaController::class, 'ajaxRecuperarPuntosVentasSelect']);
        Route::post('/ajaxRecupraActividadesSelect', [EmpresaController::class, 'ajaxRecupraActividadesSelect']);
        Route::post('/ajaxBuscarPuntoVentaNewUsuarioSelect', [EmpresaController::class, 'ajaxBuscarPuntoVentaNewUsuarioSelect']);

        Route::post('/ajaxListadoClientes', [EmpresaController::class, 'ajaxListadoClientes']);
        Route::post('/guardarClienteEmpresa', [EmpresaController::class, 'guardarClienteEmpresa']);

        Route::post('/crearCuis', [EmpresaController::class, 'crearCuis']);

        // Route::post('/ajaxListadoUsuarioEmpresa/{empresa_id}', [EmpresaController::class, 'ajaxListadoUsuarioEmpresa']);
        Route::post('/ajaxListadoUsuarioEmpresa', [EmpresaController::class, 'ajaxListadoUsuarioEmpresa']);
        Route::post('/guardarUsuarioEmpresa', [EmpresaController::class, 'guardarUsuarioEmpresa']);

        Route::post('/ajaxListadoServicios', [EmpresaController::class, 'ajaxListadoServicios']);
        Route::post('/guardarNewServioEmpresa', [EmpresaController::class, 'guardarNewServioEmpresa']);

        Route::post('/sincronizarActividades', [EmpresaController::class, 'sincronizarActividades']);
        Route::post('/sincronizarSiatProductoServicios', [EmpresaController::class, 'sincronizarSiatProductoServicios']);
        Route::post('/sincronizarPuntosVentas', [EmpresaController::class, 'sincronizarPuntosVentas']);

        Route::post('/ajaxListadoActiviadesEconomicas', [EmpresaController::class, 'ajaxListadoActiviadesEconomicas']);
        Route::post('/ajaxListadoDependeActividades', [EmpresaController::class, 'ajaxListadoDependeActividades']);

        Route::post('/ajaxListadoSiatProductosServicios', [EmpresaController::class, 'ajaxListadoSiatProductosServicios']);

        Route::get('/listadoClientes', [EmpresaController::class, 'listadoClientes']);
        Route::post('/ajaxListadoClientesEmpresa', [EmpresaController::class, 'ajaxListadoClientesEmpresa']);
        Route::post('/guardarClienteEmpresaEmpresa', [EmpresaController::class, 'guardarClienteEmpresaEmpresa']);
        Route::get('/listadoProductoServicioEmpresa', [EmpresaController::class, 'listadoProductoServicioEmpresa']);
        Route::post('/ajaxListadoProductoServicioEmpresa', [EmpresaController::class, 'ajaxListadoProductoServicioEmpresa']);
        Route::post('/guardarProductoServicioEmpresa', [EmpresaController::class, 'guardarProductoServicioEmpresa']);

    });

    Route::prefix('/sincronizacion')->group(function(){
        Route::get('/listado', [SincronizacionSiatController::class, 'listado']);
        Route::post('/ajaxListadoTipoDocumentoSector', [SincronizacionSiatController::class, 'ajaxListadoTipoDocumentoSector']);
        Route::post('/ajaxListadoTipoPuntoVenta', [SincronizacionSiatController::class, 'ajaxListadoTipoPuntoVenta']);
        Route::post('/ajaxListadoUnidadMedida', [SincronizacionSiatController::class, 'ajaxListadoUnidadMedida']);
        Route::post('/ajaxListadoTipoDocumentoIdentidad', [SincronizacionSiatController::class, 'ajaxListadoTipoDocumentoIdentidad']);
        Route::post('/ajaxListadoMetodoPago', [SincronizacionSiatController::class, 'ajaxListadoMetodoPago']);
        Route::post('/ajaxListadoTipoMoneda', [SincronizacionSiatController::class, 'ajaxListadoTipoMoneda']);
        Route::post('/ajaxListadoMotivoAnulacion', [SincronizacionSiatController::class, 'ajaxListadoMotivoAnulacion']);
        Route::post('/ajaxListadoEventoSignificativo', [SincronizacionSiatController::class, 'ajaxListadoEventoSignificativo']);


        Route::post('/sincronizarTipoDocumentoSector', [SincronizacionSiatController::class, 'sincronizarTipoDocumentoSector']);
        Route::post('/sincronizarParametricaTipoPuntoVenta', [SincronizacionSiatController::class, 'sincronizarParametricaTipoPuntoVenta']);
        Route::post('/sincronizarTipoDocumentoIdentidad', [SincronizacionSiatController::class, 'sincronizarTipoDocumentoIdentidad']);
        Route::post('/sincronizarUnidadMedida', [SincronizacionSiatController::class, 'sincronizarUnidadMedida']);
        Route::post('/sincronizarMetodoPago', [SincronizacionSiatController::class, 'sincronizarMetodoPago']);
        Route::post('/sincronizarTipoMoneda', [SincronizacionSiatController::class, 'sincronizarTipoMoneda']);
        Route::post('/sincronizarMotivoAnulacion', [SincronizacionSiatController::class, 'sincronizarMotivoAnulacion']);
        Route::post('/sincronizarEventoSignificativo', [SincronizacionSiatController::class, 'sincronizarEventoSignificativo']);

        // Route::post('/guarda', [EmpresaController::class, 'guarda']);
    });

    Route::prefix('/rol')->group(function(){
        Route::get('/listado', [RolController::class, 'listado']);
        Route::post('/ajaxListado', [RolController::class, 'ajaxListado']);
        Route::post('/agregarRol', [RolController::class, 'agregarRol']);
    });

    Route::prefix('/factura')->group(function(){
        Route::get('/formularioFacturacion', [FacturaController::class, 'formularioFacturacion']);
        Route::get('/formularioFacturacionTasaCero', [FacturaController::class, 'formularioFacturacionTasaCero']);

        Route::get('/listado', [FacturaController::class, 'listado']);

        Route::post('/ajaxListadoClientes', [FacturaController::class, 'ajaxListadoClientes']);
        Route::post('/ajaxListadoFacturas', [FacturaController::class, 'ajaxListadoFacturas']);
        Route::post('/agregarProducto', [FacturaController::class, 'agregarProducto']);
        Route::post('/ajaxListadoDetalles', [FacturaController::class, 'ajaxListadoDetalles']);
        Route::post('/descuentoPorItem', [FacturaController::class, 'descuentoPorItem']);
        Route::post('/eliminarDetalle', [FacturaController::class, 'eliminarDetalle']);
        Route::post('/descuentoAdicionalGlobal', [FacturaController::class, 'descuentoAdicionalGlobal']);
        Route::post('/verificaItemsGeneracion', [FacturaController::class, 'verificaItemsGeneracion']);
        Route::post('/arrayCuotasPagar', [FacturaController::class, 'arrayCuotasPagar']);
        // Route::post('/emitirFactura', [FacturaController::class, 'emitirFactura']);
        Route::post('/anularFactura', [FacturaController::class, 'anularFactura']);
        Route::post('/desanularFacturaAnulado', [FacturaController::class, 'desanularFacturaAnulado']);
        Route::post('/verificarNit', [FacturaController::class, 'verificarNit']);

        // Route::post('/emitirFacturaTasaCero', [FacturaController::class, 'emitirFacturaTasaCero']);

        Route::post('/sacaNumeroCafcUltimo', [FacturaController::class, 'sacaNumeroCafcUltimo']);


        // PARA LAS SINCRONIZACIONES MASA
        Route::get('/pruebas', [FacturaController::class, 'pruebas']);
        Route::get('/pruebaCompraVenta', [FacturaController::class, 'pruebaCompraVenta']);
        // PARA LAS SINCRONIZACIONES MASA

        // PARA CREACION DE FACTURAS MASA
        Route::get('/emiteFacturaMasa', [FacturaController::class, 'emiteFacturaMasa']);
        Route::get('/armaJson', [FacturaController::class, 'armaJson']);
        // PARA CREACION DE FACTURAS MASA


        Route::get('/formularioFacturacionCv', [FacturaController::class, 'formularioFacturacionCv']);
        Route::post('/ajaxListadoServicios', [FacturaController::class, 'ajaxListadoServicios']);
        Route::post('/ajaxListadoClientesBusqueda', [FacturaController::class, 'ajaxListadoClientesBusqueda']);
        Route::post('/emitirFacturaCv', [FacturaController::class, 'emitirFacturaCv']);

        Route::get('/formularioFacturacionTc', [FacturaController::class, 'formularioFacturacionTc']);
        Route::post('/emitirFacturaTc', [FacturaController::class, 'emitirFacturaTc']);

        Route::get('/generaPdfFacturaNewCv/{factura_id}', [FacturaController::class, 'generaPdfFacturaNewCv']);
        Route::get('/imprimeFactura/{factura_id}', [FacturaController::class, 'imprimeFactura']);

        // Route::post('/buscarFactura', [FacturaController::class, 'buscarFactura']);


        // Route::post('/ajaxListado', [RolController::class, 'ajaxListado']);
        // Route::post('/agregarRol', [RolController::class, 'agregarRol']);
    });

    Route::prefix('/registrocompras')->group(function(){
        Route::get('/listado', [RegistroCompraController::class, 'listado']);

        Route::post('/ajaxListado', [RegistroCompraController::class, 'ajaxListado']);
        Route::post('/agregarRegistroCompra', [RegistroCompraController::class, 'agregarRegistroCompra']);
        Route::post('/ajaxListadoRecepcion', [RegistroCompraController::class, 'ajaxListadoRecepcion']);
        Route::post('/envioPaquetesFacturasCompra', [RegistroCompraController::class, 'envioPaquetesFacturasCompra']);

    });

    Route::prefix('/suscripcion')->group(function(){

        Route::post('/ajaxListadoSuscripcion', [SuscripcionController::class, 'ajaxListadoSuscripcion']);
        Route::post('/guardarSuscripcion', [SuscripcionController::class, 'guardarSuscripcion']);


    });

    Route::prefix('/plan')->group(function(){

        Route::get('/listado', [PlanController::class, 'listado']);
        Route::post('/agregarPlan', [PlanController::class, 'agregarPlan']);
        Route::post('/ajaxListado', [PlanController::class, 'ajaxListado']);


    });

    Route::prefix('/eventosignificativo')->group(function(){

        Route::get('/listado', [EventoSignificativoController::class, 'listado']);

        Route::post('/ajaxListado', [EventoSignificativoController::class, 'ajaxListado']);
        Route::post('/buscarCufd', [EventoSignificativoController::class, 'buscarCufd']);
        Route::post('/agregarEventoSignificativo', [EventoSignificativoController::class, 'agregarEventoSignificativo']);
        Route::post('/buscarEventosSignificativos', [EventoSignificativoController::class, 'buscarEventosSignificativos']);
        Route::post('/muestraTableFacturaPaquete', [EventoSignificativoController::class, 'muestraTableFacturaPaquete']);
        Route::post('/mandarFacturasPaquete', [EventoSignificativoController::class, 'mandarFacturasPaquete']);

        // Route::post('/agregarRol', [RolController::class, 'agregarRol']);

    });

});
