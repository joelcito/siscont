<?php

namespace App\Http\Controllers;

use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\PuntoVenta;
use App\Models\Rol;
use App\Models\SiatDependeActividades;
use App\Models\SiatProductoServicio;
use App\Models\SiatTipoDocumentoSector;
use App\Models\SiatTipoPuntoVenta;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Ui\Presets\React;

class EmpresaController extends Controller
{

    public function listado(Request $request){

        $documentosSectores = SiatTipoDocumentoSector::all();

        return view('empresa.listado')->with(compact('documentosSectores', ));
    }


    /**
     * Display a listing of the resource.
     */
    public function guarda(Request $request){
        if($request->ajax()){
            // dd($request->all());
            $empresa                                            = new Empresa();
            $empresa->usuario_creador_id                        = Auth::user()->id;
            $empresa->nombre                                    = $request->input('nombre_empresa');
            $empresa->nit                                       = $request->input('nit_empresa');
            $empresa->razon_social                              = $request->input('razon_social');
            $empresa->codigo_ambiente                           = $request->input('codigo_ambiente');
            $empresa->codigo_sistema                            = $request->input('codigo_sistema');
            $empresa->codigo_documento_sector                   = $request->input('documento_sectores');
            $empresa->api_token                                 = $request->input('api_token');
            $empresa->url_facturacionCodigos                    = $request->input('url_fac_codigos');
            $empresa->url_facturacionSincronizacion             = $request->input('url_fac_sincronizacion');
            $empresa->url_servicio_facturacion_compra_venta     = $request->input('url_fac_servicios');
            $empresa->url_facturacion_operaciones               = $request->input('url_fac_operaciones');
            $empresa->url_facturacionCodigos_pro                = $request->input('url_fac_codigos_pro');
            $empresa->url_facturacionSincronizacion_pro         = $request->input('url_fac_sincronizacion_pro');
            $empresa->url_servicio_facturacion_compra_venta_pro = $request->input('url_fac_servicios_pro');
            $empresa->url_facturacion_operaciones_pro           = $request->input('url_fac_operaciones_pro');

            if($empresa->save()){
                $data['estado'] = 'success';
                $data['text']   = 'Se creo con exito';
            }else{
                $data['text']   = 'Erro al crear';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArrayEmpresa();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    protected function listadoArrayEmpresa(){
        $empresas = Empresa::all();
        return view('empresa.ajaxListado')->with(compact('empresas'))->render();
    }

    public function detalle(Request $request, $empresa_id){

        $empresa            = Empresa::find($empresa_id);
        $documentosSectores = SiatTipoDocumentoSector::all();
        $siat_tipo_ventas   = SiatTipoPuntoVenta::all();
        $roles              = Rol::all();
        $sucursales         = Sucursal::where('empresa_id', $empresa_id)
                                    ->get();



        return view('empresa.detalle')->with(compact('empresa', 'documentosSectores', 'siat_tipo_ventas', 'roles', 'sucursales'));
    }

    public function ajaxListadoSucursal(Request $request){
        if($request->ajax()){

            $empresa_id = $request->input('empresa');

            $data['estado']  = 'success';
            // $sucursales      = Sucursal::all();
            $sucursales      = Sucursal::where('empresa_id', $empresa_id)
                                        ->get();
            $data['listado'] = view('empresa.ajaxListadoSucursal')->with(compact('sucursales'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardaSucursal(Request $request){
        if($request->ajax()){

            $sucursal                     = new Sucursal();
            $sucursal->usuario_creador_id = Auth::user()->id;
            $sucursal->nombre             = $request->input('nombre_sucursal');
            $sucursal->codigo_sucursal    = $request->input('codigo_sucursal');
            $sucursal->direccion          = $request->input('direccion_sucursal');
            $sucursal->empresa_id         = $request->input('empresa_id_sucursal');

            if($sucursal->save()){

                $punto_venta                     = new PuntoVenta();
                $punto_venta->usuario_creador_id = Auth::user()->id;
                $punto_venta->sucursal_id        = $sucursal->id;
                $punto_venta->codigoPuntoVenta   = 0;
                $punto_venta->nombrePuntoVenta   = "PRIMER PUNTO VENTA";
                $punto_venta->tipoPuntoVenta     = "VENTANILLA INICIAL";
                $punto_venta->codigo_ambiente    = 2;
                $punto_venta->save();

                $data['estado'] = 'success';
                $data['text']   = 'Se creo con exito';
            }else{
                $data['text']   = 'Erro al crear';
                $data['estado'] = 'error';
            }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoPuntoVenta(Request $request){
        if($request->ajax()){

            $sucursal_id  = $request->input('sucursal');

            $punto_ventas = PuntoVenta::where('sucursal_id', $sucursal_id)
                                        ->get();

            $data['estado']  = 'success';
            $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function crearCuis(Request $request){
        if($request->ajax()){
            $punto_venta_id = $request->input('codigo_punto_venta_id_cuis');
            $sucursal_id    = $request->input('codigo_sucursal_id_cuis');

            $punto_venta = PuntoVenta::find($punto_venta_id);
            $sucursal    = Sucursal::find($sucursal_id);
            $empresa     = Empresa::find($sucursal->empresa_id);

            $siat = app(SiatController::class);

            $codigoCuis = json_decode($siat->cuis(
                $empresa->api_token,
                $empresa->url_facturacionCodigos,
                $empresa->codigo_ambiente,
                $empresa->codigo_modalidad,
                $punto_venta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $empresa->nit
            ));

            // dd($codigoCuis);

            if($codigoCuis->estado === "success"){
                // dd($codigoCuis);
                // session(['scuis'                => $codigoCuis->resultado->RespuestaCuis->codigo]);
                // session(['sfechaVigenciaCuis'   => $codigoCuis->resultado->RespuestaCuis->fechaVigencia]);

                if($codigoCuis->resultado->RespuestaCuis->transaccion){
                    $codigoCuisGenerado    = $codigoCuis->resultado->RespuestaCuis->codigo;
                    $fechaVigenciaGenerado = $codigoCuis->resultado->RespuestaCuis->fechaVigencia;

                    $cuisSacado = Cuis::where('punto_venta_id', $punto_venta->id)
                                        ->where('sucursal_id', $sucursal->id)
                                        ->where('codigo', $codigoCuisGenerado)
                                        ->first();

                    if(is_null($cuisSacado)){
                        $cuis                     = new Cuis();
                        $cuis->usuario_creador_id = Auth::user()->id;
                        $cuis->punto_venta_id     = $punto_venta->id;
                        $cuis->sucursal_id        = $sucursal->id;
                        $cuis->codigo             = $codigoCuisGenerado;
                        $cuis->fechaVigencia      = $fechaVigenciaGenerado;
                        $cuis->codigo_ambiente    = $empresa->codigo_ambiente;
                        if($cuis->save()){
                            $data['text']   = 'Se creo el CUIS con exito';
                            $data['estado'] = 'success';
                        }else{
                            $data['text']   = 'Error al crear el CUIS';
                            $data['estado'] = 'error';
                        }
                    }else{
                        $data['text']   = 'Ya existe un CUIS del punto de Venta y Sucursal';
                        $data['estado'] = 'warnig';
                    }
                }else{
                    if(isset($codigoCuis->resultado->RespuestaCuis->codigo)){
                        if($codigoCuis->resultado->RespuestaCuis->mensajesList->codigo == 980){
                            $codigoCuisGenerado    = $codigoCuis->resultado->RespuestaCuis->codigo;
                            $fechaVigenciaGenerado = $codigoCuis->resultado->RespuestaCuis->fechaVigencia;
    
                            $cuisSacado = Cuis::where('punto_venta_id', $punto_venta->id)
                                                ->where('sucursal_id', $sucursal->id)
                                                ->where('codigo', $codigoCuisGenerado)
                                                ->first();
    
                            if(is_null($cuisSacado)){
                                $cuis                     = new Cuis();
                                $cuis->usuario_creador_id = Auth::user()->id;
                                $cuis->punto_venta_id     = $punto_venta->id;
                                $cuis->sucursal_id        = $sucursal->id;
                                $cuis->codigo             = $codigoCuisGenerado;
                                $cuis->fechaVigencia      = $fechaVigenciaGenerado;
                                $cuis->codigo_ambiente    = $empresa->codigo_ambiente;
                                if($cuis->save()){
                                    $data['text']   = 'Se creo el CUIS con exito';
                                    $data['estado'] = 'success';
                                }else{
                                    $data['text']   = 'Error al crear el CUIS';
                                    $data['estado'] = 'error';
                                }
                            }else{
                                $data['text']   = 'Ya existe un CUIS del punto de Venta y Sucursal';
                                $data['estado'] = 'warnig';
                            }
                        }else{
                            $data['text']   = 'Error al crear el CUIS';
                            $data['msg']    = $codigoCuis->resultado->RespuestaCuis;
                            $data['estado'] = 'error';
                        }
                    }else{
                        $data['text']   = 'Error al crear el CUIS';
                        $data['msg']    = $codigoCuis->resultado->RespuestaCuis;
                        $data['estado'] = 'error';
                    }
                }
            }else{
                $data['text']   = 'Error en la consulta';
                $data['msg']    = $codigoCuis;
                $data['estado'] = 'error';
            }
           
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardaPuntoVenta(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $empresa_id                      = $request->input('empresa_id_punto_venta');
            $sucursal_id                     = $request->input('sucursal_id_punto_venta');
            $codigo_clasificador_punto_venta = $request->input('codigo_tipo_punto_id_punto_venta');

            $empresa    = Empresa::find($empresa_id);
            $sucursal   = Sucursal::find($sucursal_id);

            $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                    ->first();

            $cuis       = Cuis::where('punto_venta_id', $puntoVenta->id)
                              ->where('sucursal_id', $sucursal->id)
                              ->where('codigo_ambiente', $empresa->codigo_ambiente)
                              ->first();


            $descripcionPuntoVenta = $request->input('descripcion_punto_venta');
            $nombrePuntoVenta      = $request->input('nombre_punto_venta');
            $header                = $empresa->api_token;
            $url4                  = $empresa->url_facturacion_operaciones;
            $codigoAmbiente        = $empresa->codigo_ambiente;
            $codigoModalidad       = $empresa->cogigo_modalidad;
            $codigoSistema         = $empresa->codigo_sistema;
            $codigoSucursal        = $sucursal->codigo_sucursal;
            $codigoTipoPuntoVenta  = $codigo_clasificador_punto_venta;
            $scuis                 = $cuis->codigo;
            $nit                   = $empresa->nit;

            $siat = app(SiatController::class);

            $puntoVentaGenerado = json_decode($siat->registroPuntoVenta(
                $descripcionPuntoVenta,
                $nombrePuntoVenta,
                $header,
                $url4,
                $codigoAmbiente,
                $codigoModalidad,
                $codigoSistema,
                $codigoSucursal,
                $codigoTipoPuntoVenta,
                $scuis,
                $nit
            ));

            if($puntoVentaGenerado->estado === "success"){
                if($puntoVentaGenerado->resultado->RespuestaRegistroPuntoVenta->transaccion){
                    $codigoPuntoVentaDevuelto        = $puntoVentaGenerado->resultado->RespuestaRegistroPuntoVenta->codigoPuntoVenta;

                    $punto_venta                     = new PuntoVenta();
                    $punto_venta->usuario_creador_id = Auth::user()->id;
                    $punto_venta->sucursal_id        = $sucursal->id;
                    $punto_venta->codigoPuntoVenta   = $codigoPuntoVentaDevuelto;
                    $punto_venta->nombrePuntoVenta   = $nombrePuntoVenta;
                    $punto_venta->tipoPuntoVenta     = $codigo_clasificador_punto_venta;
                    $punto_venta->codigo_ambiente    = $codigoAmbiente;

                    if($punto_venta->save()){
                        $data['text']   = 'Se creo el PUNTO DE VENTA con exito';
                        $data['estado'] = 'success';

                        $punto_ventas = PuntoVenta::where('sucursal_id', $sucursal->id)
                                                    ->get();
                        $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas'))->render();

                    }else{
                        $data['text']   = 'Error al crear el PUNTO DE VENTA';
                        $data['estado'] = 'error';
                    }
                }else{
                    $data['text']   = 'Error al crear el CUIS';
                    $data['msg']    = $puntoVentaGenerado->resultado;
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = 'Error en la consulta';
                $data['msg']    = $puntoVentaGenerado;
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    // public function ajaxListadoUsuarioEmpresa(Request $request, $empresa_id){
    public function ajaxListadoUsuarioEmpresa(Request $request){
        if($request->ajax()){
            // dd($request->all(), $empresa_id);
            $empresa_id = $request->input('empresa');

            $usuarios = User::where('empresa_id', $empresa_id)
                            ->get();

                            
            $data['listado']   = view('empresa.ajaxListadoUsuarioEmpresa')->with(compact('usuarios'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardarUsuarioEmpresa(Request $request){
        if($request->ajax()){

            $usuario = new User();

            $usuario->usuario_creador_id = Auth::user()->id;
            $usuario->nombres            = $request->input('nombres_new_usuaio_empresa');
            $usuario->ap_paterno         = $request->input('ap_paterno_new_usuaio_empresa');
            $usuario->ap_materno         = $request->input('ap_materno_new_usuaio_empresa');
            $usuario->name               = $usuario->nombres." ".$usuario->ap_paterno." ".$usuario->ap_materno;
            $usuario->email              = $request->input('usuario_new_usuaio_empresa');
            $usuario->password           = Hash::make($request->input('contrasenia_new_usuaio_empresa'));
            $usuario->empresa_id         = $request->input('empresa_id_new_usuario_empresa');
            $usuario->rol_id             = $request->input('rol_id_new_usuaio_empresa');
            $usuario->numero_celular     = $request->input('num_ceular_new_usuaio_empresa');

            $usuario->save();

            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return  $data;
    }

    public function ajaxListadoServicios(Request $request){
        if($request->ajax()){

            $empresa_id = $request->input('empresa');

            $servicios = SiatProductoServicio::where('empresa_id', $empresa_id)
                                            ->get();

            $data['listado']   = view('empresa.ajaxListadoServicios')->with(compact('servicios'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoDependeActividades(Request $request){
        if($request->ajax()){

            $empresa_id  = $request->input('empresa');
            $actividades = SiatDependeActividades::where('empresa_id', $empresa_id)
                                                ->get();
            $data['listado']   = view('empresa.ajaxListadoDependeActividades')->with(compact('actividades'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function  sincronizarActividades(Request $request){
        if($request->ajax()){

            // dd($request->all());
            $empresa_id     = $request->input('empresa_id_sincronizar_actividad');
            $punto_venta_id = $request->input('punto_venta_id_sincronizar_actividad');
            $sucursal_id   = $request->input('sucuarsal_id_sincronizar_actividad');

            $empresa    = Empresa::find($empresa_id);
            $puntoVenta = PuntoVenta::find($punto_venta_id);
            $sucursal  = Sucursal::find($sucursal_id);

            $cuis       = Cuis::where('punto_venta_id', $puntoVenta->id)
                              ->where('sucursal_id', $sucursal->id)
                              ->where('codigo_ambiente', $empresa->codigo_ambiente)
                              ->first();

            $siat = app(SiatController::class);

            $header           = $empresa->api_token;
            $url2             = $empresa->url_facturacionSincronizacion;
            $codigoAmbiente   = $empresa->codigo_ambiente;
            $codigoPuntoVenta = $puntoVenta->codigoPuntoVenta;
            $codigoSistema    = $empresa->codigo_sistema;
            $codigoSucursal   = $sucursal->codigo_sucursal;
            $scuis            = $cuis->codigo;
            $nit              = $empresa->nit;

            $sincronizarActiviades = json_decode($siat->sincronizarActividades(
                $header,
                $url2,
                $codigoAmbiente,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $scuis,
                $nit
            ));

            if($sincronizarActiviades->estado === "success"){
                if($sincronizarActiviades->resultado->RespuestaListaActividades->transaccion){

                    
                    $listaActividades = $sincronizarActiviades->resultado->RespuestaListaActividades->listaActividades;
                    if(is_array($listaActividades)){
                        // dd($sincronizarActiviades->resultado->RespuestaListaActividades->listaActividades);

                        foreach ($listaActividades as $key => $actividad) {
                            $codigoCaeb       = $actividad->codigoCaeb;
                            $descripcion      = $actividad->descripcion;
                            $tipoActividad    = $actividad->tipoActividad;

                            $activiadesEconomica = SiatDependeActividades::where('empresa_id',$empresa_id )
                                                                        ->where('sucursal_id',$sucursal_id )
                                                                        ->where('punto_venta_id', $punto_venta_id)
                                                                        ->where('codigo_caeb', $codigoCaeb)
                                                                        // ->where('descripcion', $descripcion)
                                                                        ->where('codigo_ambiente', $empresa->codigo_ambiente)
                                                                        ->where('tipo_actividad', $tipoActividad)
                                                                        // ->get();
                                                                        ->first();

                            if(is_null($activiadesEconomica)){

                                $activiadesEconomica                  = new SiatDependeActividades();
                                $activiadesEconomica->empresa_id      = $empresa_id;
                                $activiadesEconomica->sucursal_id     = $sucursal_id;
                                $activiadesEconomica->punto_venta_id  = $punto_venta_id;
                                $activiadesEconomica->codigo_ambiente = $empresa->codigo_ambiente;
                                $activiadesEconomica->codigo_caeb     = $codigoCaeb;
                                $activiadesEconomica->descripcion     = $descripcion;
                                $activiadesEconomica->tipo_actividad  = $tipoActividad;
        
                            }else{
                                $activiadesEconomica->descripcion = $descripcion;
                            }
        
                            $activiadesEconomica->save();
                        }

                    }else{
                        $codigoCaeb       = $listaActividades->codigoCaeb;
                        $descripcion      = $listaActividades->descripcion;
                        $tipoActividad    = $listaActividades->tipoActividad;
    
                        $activiadesEconomica = SiatDependeActividades::where('empresa_id',$empresa_id )
                                                                        ->where('sucursal_id',$sucursal_id )
                                                                        ->where('punto_venta_id', $punto_venta_id)
                                                                        ->where('codigo_caeb', $codigoCaeb)
                                                                        // ->where('descripcion', $descripcion)
                                                                        ->where('codigo_ambiente', $empresa->codigo_ambiente)
                                                                        ->where('tipo_actividad', $tipoActividad)
                                                                        // ->get();
                                                                        ->first();

                        if(is_null($activiadesEconomica)){

                            $activiadesEconomica                  = new SiatDependeActividades();
                            $activiadesEconomica->empresa_id      = $empresa_id;
                            $activiadesEconomica->sucursal_id     = $sucursal_id;
                            $activiadesEconomica->punto_venta_id  = $punto_venta_id;
                            $activiadesEconomica->codigo_ambiente = $empresa->codigo_ambiente;
                            $activiadesEconomica->codigo_caeb     = $codigoCaeb;
                            $activiadesEconomica->descripcion     = $descripcion;
                            $activiadesEconomica->tipo_actividad  = $tipoActividad;
    
                        }else{
                            $activiadesEconomica->descripcion = $descripcion;
                        }
    
                        $activiadesEconomica->save();
                    }

                    $actividades = SiatDependeActividades::where('empresa_id', $empresa_id)
                                                        ->where('sucursal_id', $sucursal_id)
                                                        ->where('punto_venta_id',$punto_venta_id)
                                                        ->get();

                    $data['listado']   = view('empresa.ajaxListadoActiviadesEconomicas')->with(compact('actividades', 'sucursal_id', 'punto_venta_id'))->render();
                    $data['estado'] = 'success';
                    
                }else{
                    $data['text']    = $sincronizarActiviades->resultado->RespuestaListaActividades->mensajesList;
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = 'Error con la funcion';
                $data['estado'] = 'error';
            }

            // dd(
            //     $sincronizarActiviades,
            //     $header,
            //     $url2,
            //     $codigoAmbiente,
            //     $codigoPuntoVenta,
            //     $codigoSistema,
            //     $codigoSucursal,
            //     $scuis,
            //     $nit
            // );

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoActiviadesEconomicas(Request $request){
        if($request->ajax()){

            $empresa_id     = $request->input('empresa');
            $sucursal_id    = $request->input('sucursal_id');
            $punto_venta_id = $request->input('punto_venta_id');

            $actividades = SiatDependeActividades::where('empresa_id', $empresa_id)
                                                ->where('sucursal_id', $sucursal_id)
                                                ->where('punto_venta_id',$punto_venta_id)
                                                ->get();

            $data['listado']   = view('empresa.ajaxListadoActiviadesEconomicas')->with(compact('actividades', 'sucursal_id', 'punto_venta_id'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    function ajaxRecuperarPuntosVentasSelect(Request $request){
        if($request->ajax()){
            $sucursal_id = $request->input('sucursal_id');
            $punto_ventas = PuntoVenta::where('sucursal_id', $sucursal_id)
                                    ->get();
            $select = '<select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="new_servicio_sucursal_id" id="new_servicio_sucursal_id" data-dropdown-parent="#modal_new_servicio" onchange="ajaxRecupraActividadesSelect(this)">';
            $option = '<option></option>';
            foreach ($punto_ventas as $key => $value) {
                $option = $option.'<option value="'.$value->id.'">'.$value->nombrePuntoVenta.'</option>';
            }
            $select = $select.$option.'</select>';
            $data['estado'] = 'success';
            $data['select'] = $select;
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    function  ajaxRecupraActividadesSelect(Request $request){
        if($request->ajax()){

            $punto_venta_id = $request->input('punto_venta_id');
            $empresa_id     = $request->input('empresa_id');
            $sucursal_id    = $request->input('sucursal_id');

            $activiadesEconomica = SiatDependeActividades::where('empresa_id',$empresa_id)
                                                        ->where('sucursal_id', $sucursal_id)
                                                        ->where('punto_venta_id', $punto_venta_id)
                                                        ->get();

            $select = '<select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="new_servicio_codigo_actividad_economica" id="new_servicio_codigo_actividad_economica" data-dropdown-parent="#modal_new_servicio">';
            $option = '<option></option>';
            foreach ($activiadesEconomica as $key => $value) {
                $option = $option.'<option value="'.$value->codigo_caeb.'">'.$value->descripcion.'</option>';
            }
            $select = $select.$option.'</select>';
            $data['estado'] = 'success';
            $data['select'] = $select;

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    function ajaxListadoSiatProductosServicios(Request $request){
        if($request->ajax()){

            $empresa_id  = $request->input('empresa_id');
            $punto_venta = $request->input('punto_venta');
            $sucursal    = $request->input('sucursal');

            $siatProductosServicios = SiatProductoServicio::where('empresa_id', $empresa_id)
                                                            ->where('punto_venta_id',$punto_venta)
                                                            ->where('sucursal_id', $sucursal)
                                                            ->get();

            $data['listado']   = view('empresa.ajaxListadoSiatProductosServicios')->with(compact('siatProductosServicios', 'punto_venta', 'sucursal'))->render();
            $data['estado'] = 'success';                          

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    function sincronizarSiatProductoServicios(Request $request){
        if($request->ajax()){

            $empresa_id     = $request->input('empresa_id');
            $punto_venta_id = $request->input('punto_venta');
            $sucursal_id    = $request->input('sucursal');

            $empresa    = Empresa::find($empresa_id);
            $puntoVenta = PuntoVenta::find($punto_venta_id);
            $sucursal   = Sucursal::find($sucursal_id);

            $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

            $header           = $empresa->api_token;
            $url2             = $empresa->url_facturacionSincronizacion;
            $codigoAmbiente   = $empresa->codigo_ambiente;
            $codigoPuntoVenta = $puntoVenta->codigoPuntoVenta;
            $codigoSistema    = $empresa->codigo_sistema;
            $codigoSucursal   = $sucursal->codigo_sucursal;
            $cuis             = $cuis->codigo;
            $nit              = $empresa->nit;

            $siat = app(SiatController::class);

            $sincronizarListaProductosServicios = json_decode($siat->sincronizarListaProductosServicios(
                $header,
                $url2,
                $codigoAmbiente,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $cuis,
                $nit
            ));

            // dd($sincronizarListaProductosServicios,$sincronizarListaProductosServicios->resultado->RespuestaListaProductos);

            if($sincronizarListaProductosServicios->estado === "success"){
                if($sincronizarListaProductosServicios->resultado->RespuestaListaProductos->transaccion){
                    $listaCodigo = $sincronizarListaProductosServicios->resultado->RespuestaListaProductos->listaCodigos;
                    if(is_array($listaCodigo)){
                        
                        foreach ($listaCodigo as $key => $value) {

                            $listadoProductoServicio = SiatProductoServicio::where('empresa_id',$empresa_id)
                                                                            ->where('punto_venta_id',$punto_venta_id)
                                                                            ->where('sucursal_id',$sucursal_id)
                                                                            ->where('codigo_ambiente',$empresa->codigo_ambiente)
                                                                            ->where('codigo_actividad',$value->codigoActividad)
                                                                            ->where('codigo_producto',$value->codigoProducto)
                                                                            ->first();

                            if(is_null($listadoProductoServicio)){
                                $listadoProductoServicio                       = new SiatProductoServicio();
                                $listadoProductoServicio->usuario_creador_id   = Auth::user()->id;
                                $listadoProductoServicio->empresa_id           = $empresa_id;
                                $listadoProductoServicio->punto_venta_id       = $punto_venta_id;
                                $listadoProductoServicio->sucursal_id          = $sucursal_id;
                                $listadoProductoServicio->codigo_ambiente      = $empresa->codigo_ambiente;
                                $listadoProductoServicio->codigo_actividad     = $value->codigoActividad;
                                $listadoProductoServicio->codigo_producto      = $value->codigoProducto;
                                $listadoProductoServicio->codigo_producto      = $value->codigoProducto;
                                $listadoProductoServicio->descripcion_producto = $value->descripcionProducto;
                                
                            }else{
                                $listadoProductoServicio->usuario_modificador_id = Auth::user()->id;
                                $listadoProductoServicio->codigo_actividad       = $value->codigoActividad;
                                $listadoProductoServicio->codigo_producto        = $value->codigoProducto;
                                $listadoProductoServicio->codigo_producto        = $value->codigoProducto;
                                $listadoProductoServicio->descripcion_producto   = $value->descripcionProducto;
                            }
                            $listadoProductoServicio->save();
                        }
                    }else{

                        $listadoProductoServicio = SiatProductoServicio::where('empresa_id',$empresa_id)
                                                                            ->where('punto_venta_id',$punto_venta_id)
                                                                            ->where('sucursal_id',$sucursal_id)
                                                                            ->where('codigo_ambiente',$empresa->codigo_ambiente)
                                                                            ->where('codigo_actividad',$listaCodigo->codigoActividad)
                                                                            ->where('codigo_producto',$listaCodigo->codigoActividad)
                                                                            ->first();

                        if(is_null($listadoProductoServicio)){
                            $listadoProductoServicio                       = new SiatProductoServicio();
                            $listadoProductoServicio->usuario_creador_id   = Auth::user()->id;
                            $listadoProductoServicio->empresa_id           = $empresa_id;
                            $listadoProductoServicio->punto_venta_id       = $punto_venta_id;
                            $listadoProductoServicio->sucursal_id          = $sucursal_id;
                            $listadoProductoServicio->codigo_ambiente      = $empresa->codigo_ambiente;
                            $listadoProductoServicio->codigo_actividad     = $listaCodigo->codigoActividad;
                            $listadoProductoServicio->codigo_producto      = $listaCodigo->codigoProducto;
                            $listadoProductoServicio->codigo_producto      = $listaCodigo->codigoProducto;
                            $listadoProductoServicio->descripcion_producto = $listaCodigo->descripcionProducto;
                            
                        }else{
                            $listadoProductoServicio->usuario_modificador_id = Auth::user()->id;
                            $listadoProductoServicio->codigo_actividad       = $listaCodigo->codigoActividad;
                            $listadoProductoServicio->codigo_producto        = $listaCodigo->codigoProducto;
                            $listadoProductoServicio->codigo_producto        = $listaCodigo->codigoProducto;
                            $listadoProductoServicio->descripcion_producto   = $listaCodigo->descripcionProducto;
                        }
                        $listadoProductoServicio->save();
                    }
                    
                    // $empresa_id  = $request->input('empresa_id');
                    // $punto_venta = $request->input('punto_venta');
                    // $sucursal    = $request->input('sucursal');

                    $siatProductosServicios = SiatProductoServicio::where('empresa_id', $empresa_id)
                                                                    ->where('punto_venta_id',$punto_venta_id)
                                                                    ->where('sucursal_id', $sucursal_id)
                                                                    ->get();

                    $punto_venta = $punto_venta_id;
                    $sucursal    = $sucursal_id;

                    $data['listado']   = view('empresa.ajaxListadoSiatProductosServicios')->with(compact('siatProductosServicios', 'punto_venta', 'sucursal'))->render();
                    $data['estado'] = 'success';  

                }else{
                    $data['text']    = $sincronizarListaProductosServicios->resultado->RespuestaListaActividades->mensajesList;
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']    = $sincronizarListaProductosServicios;
                $data['estado'] = 'error';
            }

            // dd($sincronizarListaProductosServicios);

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }
}
