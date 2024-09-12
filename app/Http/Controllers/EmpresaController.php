<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\EmpresaDocumentoSector;
use App\Models\Plan;
use App\Models\PuntoVenta;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\SiatDependeActividades;
use App\Models\SiatProductoServicio;
use App\Models\SiatTipoDocumentoSector;
use App\Models\SiatTipoPuntoVenta;
use App\Models\SiatUnidadMedida;
use App\Models\Sucursal;
use App\Models\Suscripcion;
use App\Models\UrlApiServicioSiat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Ui\Presets\React;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

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
            $empresa_id = $request->input('empresa_id');

            if($empresa_id === "0"){
                $empresa                                            = new Empresa();
                $empresa->usuario_creador_id                        = Auth::user()->id;
            }else{
                $empresa                         = Empresa::find($empresa_id);
                $empresa->usuario_modificador_id = Auth::user()->id;
            }

            $empresa->nombre                                = $request->input('nombre_empresa');
            $empresa->nit                                   = $request->input('nit_empresa');
            $empresa->razon_social                          = $request->input('razon_social');
            $empresa->codigo_ambiente                       = $request->input('codigo_ambiente');
            $empresa->codigo_modalidad                      = $request->input('codigo_modalidad');
            $empresa->codigo_sistema                        = $request->input('codigo_sistema');
            $empresa->codigo_documento_sector               = $request->input('documento_sectores');
            $empresa->api_token                             = $request->input('api_token');
            $empresa->url_facturacionCodigos                = $request->input('url_fac_codigos');
            $empresa->url_facturacionSincronizacion         = $request->input('url_fac_sincronizacion');
            $empresa->url_servicio_facturacion_compra_venta = $request->input('url_fac_servicios');
            $empresa->url_facturacion_operaciones           = $request->input('url_fac_operaciones');
            $empresa->municipio                             = $request->input('municipio');
            $empresa->celular                               = $request->input('celular');
            $empresa->cafc                                  = $request->input('codigo_cafc');

            if($request->has('fila_archivo_p12')){
                // Obtén el archivo de la solicitud
                $file = $request->file('fila_archivo_p12');

                // Define el nombre del archivo y el directorio de almacenamiento
                $originalName = $file->getClientOriginalName();
                $filename     = time() . '_'. str_replace(' ', '_', $originalName);
                $directory    = 'assets/docs/certificate';

                // Guarda el archivo en el directorio especificado
                $file->move(public_path($directory), $filename);

                // Obtén la ruta completa del archivo
                $filePath = $directory . '/' . $filename;

                // Guarda la ruta del archivo en la base de datos
                $empresa->archivop12 = $filePath;
                $empresa->contrasenia = $request->input('contrasenia_archivo_p12');
            }

            if($request->has('logo_empresa')){
                $foto = $request->file('logo_empresa');

                // Define el nombre del archivo y el directorio de almacenamiento
                $originalName = $foto->getClientOriginalName();
                $filename     = time() . '_'. str_replace(' ', '_', $originalName);
                $directory    = 'assets/img';

                // Guarda el archivo en el directorio especificado
                $foto->move(public_path($directory), $filename);

                // Obtén la ruta completa del archivo
                // $filePath = $directory . '/' . $filename;
                $filePath = $filename;

                // Guarda la ruta del archivo en la base de datos
                $empresa->logo = $filePath;
                // $empresa->contrasenia = $request->input('contrasenia_archivo_p12');

            }

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
        $sucursales         = Sucursal::where('empresa_id', $empresa_id)->get();

        $activiadesEconomica = SiatDependeActividades::where('empresa_id', $empresa_id)->get();
        $productoServicio    = SiatProductoServicio::where('empresa_id', $empresa_id)->get();
        $unidadMedida        = SiatUnidadMedida::all();

        $planes = Plan::all();



        // $punto_ventas = PuntoVenta::where('empre')->get();


        return view('empresa.detalle')->with(compact(
            'empresa',
            'documentosSectores',
            'siat_tipo_ventas',
            'roles',
            'sucursales',
            'activiadesEconomica',
            'productoServicio',
            'unidadMedida',
            'planes'
        ));
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

            $sucursal_id = $request->input('sucursal_id_sucursal');
            $usuario     = Auth::user();

            if($sucursal_id == "0"){
                $sucursal                     = new Sucursal();
                $sucursal->usuario_creador_id = $usuario->id;
            }else{
                $sucursal                         = new Sucursal();
                $sucursal->usuario_modificador_id = $usuario->id;
            }

            $sucursal->nombre             = $request->input('nombre_sucursal');
            $sucursal->codigo_sucursal    = $request->input('codigo_sucursal');
            $sucursal->direccion          = $request->input('direccion_sucursal');
            $sucursal->empresa_id         = $request->input('empresa_id_sucursal');

            if($sucursal->save()){

                $punto_venta                     = new PuntoVenta();
                $punto_venta->usuario_creador_id = Auth::user()->id;
                $punto_venta->sucursal_id        = $sucursal->id;
                $punto_venta->codigoPuntoVenta   = 0;
                $punto_venta->nombrePuntoVenta   = "PRIMER PUNTO VENTA POR DEFECTO";
                $punto_venta->tipoPuntoVenta     = "VENTANILLA INICIAL POR DEFECTO";
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
            $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas', 'sucursal_id'))->render();
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

             // Obtener la instancia del modelo
            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlCodigos         = $urlApiServicioSiat->getUrlCodigos($empresa->codigo_ambiente, $empresa->codigo_modalidad);
            // dd($empresa->codigo_ambiente, $empresa->codigo_modalidad, $UrlCodigos);

            if($UrlCodigos){

                $siat = app(SiatController::class);

                $codigoCuis = json_decode($siat->cuis(
                    $empresa->api_token,
                    $UrlCodigos->url_servicio,
                    $empresa->codigo_ambiente,
                    $empresa->codigo_modalidad,
                    $punto_venta->codigoPuntoVenta,
                    $empresa->codigo_sistema,
                    $sucursal->codigo_sucursal,
                    $empresa->nit
                ));

                // dd(
                //     // $codigoCuis,
                //     // $empresa,
                //     $empresa->api_token,
                //     $UrlCodigos->url_servicio,
                //     $empresa->codigo_ambiente,
                //     $empresa->codigo_modalidad,
                //     $punto_venta->codigoPuntoVenta,
                //     $empresa->codigo_sistema,
                //     $sucursal->codigo_sucursal,
                //     $empresa->nit
                // );

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
                            // $cuis->fechaVigencia      = $fechaVigenciaGenerado;
                            $cuis->fechaVigencia     = Carbon::parse($fechaVigenciaGenerado)->format('Y-m-d H:i:s');

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
                                    // $cuis->fechaVigencia      = $fechaVigenciaGenerado;
                                    $cuis->fechaVigencia     = Carbon::parse($fechaVigenciaGenerado)->format('Y-m-d H:i:s');
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
                $data['msg']   = 'No existe el servico para la generacion el CUIS';
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

            // dd($request->all());

            $usuario = new User();

            $usuario->usuario_creador_id = Auth::user()->id;
            $usuario->nombres            = $request->input('nombres_new_usuaio_empresa');
            $usuario->ap_paterno         = $request->input('ap_paterno_new_usuaio_empresa');
            $usuario->ap_materno         = $request->input('ap_materno_new_usuaio_empresa');
            $usuario->name               = $usuario->nombres." ".$usuario->ap_paterno." ".$usuario->ap_materno;
            $usuario->email              = $request->input('usuario_new_usuaio_empresa');
            $usuario->password           = Hash::make($request->input('contrasenia_new_usuaio_empresa'));
            $usuario->empresa_id         = $request->input('empresa_id_new_usuario_empresa');
            $usuario->punto_venta_id     = $request->input('punto_venta_id_new_usuaio_empresa');
            $usuario->sucursal_id        = $request->input('sucursal_id_new_usuaio_empresa');
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

            $servicios = Servicio::where('empresa_id', $empresa_id)
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

            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlSincronizacion  = $urlApiServicioSiat->getUrlSincronizacion($empresa->codigo_ambiente, $empresa->codigo_modalidad);

            $siat = app(SiatController::class);

            $header           = $empresa->api_token;
            $url2             = $UrlSincronizacion->url_servicio;
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

            $sucursal_id  = $request->input('sucursal_id');
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

            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlSincronizacion  = $urlApiServicioSiat->getUrlSincronizacion($empresa->codigo_ambiente, $empresa->codigo_modalidad);

            $header           = $empresa->api_token;
            $url2             = $UrlSincronizacion->url_servicio;
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

                    $siatProductosServicios = SiatProductoServicio::where('empresa_id', $empresa_id)
                                                                    ->where('punto_venta_id',$punto_venta_id)
                                                                    ->where('sucursal_id', $sucursal_id)
                                                                    ->get();

                    $punto_venta = $punto_venta_id;
                    $sucursal    = $sucursal_id;

                    $data['listado'] = view('empresa.ajaxListadoSiatProductosServicios')->with(compact('siatProductosServicios', 'punto_venta', 'sucursal'))->render();
                    $data['estado']  = 'success';
                    $data['text']    = 'Se Sincronizo con exito!';

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

    public function sincronizarPuntosVentas(Request $request){
        if($request->ajax()){

            $empresa_id  = $request->input('empresa_id');
            $sucursal_id = $request->input('sucursal');

            $empresa     = Empresa::find($empresa_id);
            $sucursal    = Sucursal::find($sucursal_id);
            $punto_venta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                        ->where('codigoPuntoVenta',0)
                                        ->first();

            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlSincronizacion  = $urlApiServicioSiat->getUrlOperaciones($empresa->codigo_ambiente, $empresa->codigo_modalidad);

            $header         = $empresa->api_token;
            $url4           = $UrlSincronizacion->url_servicio;
            $codigoAmbiente = $empresa->codigo_ambiente;
            $codigoSistema  = $empresa->codigo_sistema;
            $codigoSucursal = $sucursal->codigo_sucursal;

            $cuis  = $empresa->cuisVigente($sucursal->id, $punto_venta->id, $empresa->codigo_ambiente);

            if($cuis){
                $scuis = $cuis->codigo;
                $nit   = $empresa->nit;
                $siat = app(SiatController::class);
                $consultaPuntoVenta = json_decode($siat->consultaPuntoVenta(
                    $header,
                    $url4,
                    $codigoAmbiente,
                    $codigoSistema,
                    $codigoSucursal,
                    $scuis,
                    $nit
                ));
                if($consultaPuntoVenta->estado === "success"){
                    if($consultaPuntoVenta->resultado->RespuestaConsultaPuntoVenta->transaccion){
                        $listaPuntosVentas = $consultaPuntoVenta->resultado->RespuestaConsultaPuntoVenta->listaPuntosVentas;
                        foreach ($listaPuntosVentas as $key => $value) {

                            $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                                    ->where('codigoPuntoVenta', $value->codigoPuntoVenta)
                                                    ->where('codigo_ambiente', $empresa->codigo_ambiente)
                                                    ->first();

                            if(is_null($puntoVenta)){
                                $puntoVenta                     = new PuntoVenta();
                                $puntoVenta->usuario_creador_id = Auth::user()->id;
                                $puntoVenta->sucursal_id        = $sucursal->id;
                                $puntoVenta->codigoPuntoVenta   = $value->codigoPuntoVenta;
                                $puntoVenta->nombrePuntoVenta   = $value->nombrePuntoVenta;
                                $puntoVenta->tipoPuntoVenta     = $value->tipoPuntoVenta;
                                $puntoVenta->codigo_ambiente    = $empresa->codigo_ambiente;
                                $puntoVenta->save();
                            }
                        }
                        $data['estado'] = 'success';
                        $sucursal_id  = $sucursal->id;
                        $punto_ventas = PuntoVenta::where('sucursal_id', $sucursal->id)
                                                    ->get();
                        $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas', 'sucursal_id'))->render();
                    }else{
                        $data['text']    = $consultaPuntoVenta->resultado;
                        $data['estado'] = 'error';
                    }
                }else{
                    $data['text']   = $consultaPuntoVenta;
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = "Cuis no encontrado.";
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardarNewServioEmpresa(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $servicio_id_new_servicio = $request->input('servicio_id_new_servicio');

            if($servicio_id_new_servicio == "0"){
                $servicio                     = new Servicio();
                $servicio->usuario_creador_id = Auth::user()->id;
            }else{
                $servicio                         = Servicio::find($servicio_id_new_servicio);
                $servicio->usuario_modificador_id = Auth::user()->id;
            }

            $servicio->empresa_id                  = $request->input('empresa_id_new_servicio');
            $servicio->siat_depende_actividades_id = $request->input('actividad_economica_siat_id_new_servicio');
            $servicio->siat_producto_servicios_id  = $request->input('producto_servicio_siat_id_new_servicio');
            $servicio->siat_unidad_medidas_id      = $request->input('unidad_medida_siat_id_new_servicio');
            $servicio->descripcion                 = $request->input('descrpcion_new_servicio');
            $servicio->precio                      = $request->input('precio_new_servicio');

            $servicio->save();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function  ajaxBuscarPuntoVentaNewUsuarioSelect(Request $request) {
        if($request->ajax()){
            $sucursal_id   = $request->input('sucursal_id');
            $puntos_ventas = PuntoVenta::where('sucursal_id', $sucursal_id)
                                        ->get();

            $select = '<select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="punto_venta_id_new_usuaio_empresa" id="punto_venta_id_new_usuaio_empresa" data-dropdown-parent="#modal_new_usuario">';
            $option = '<option></option>';
            foreach ($puntos_ventas as $key => $value) {
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

    public function  ajaxListadoClientes(Request $request) {
        if($request->ajax()){

            $empresa_id = $request->input('empresa');

            $clientes = Cliente::where('empresa_id', $empresa_id)
                                ->get();

            $data['listado'] = view('empresa.ajaxListadoClientes')->with(compact('clientes'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function  guardarClienteEmpresa(Request $request){
        if($request->ajax()){

            $cliente                     = new Cliente();
            $cliente->usuario_creador_id = Auth::user()->id;
            $cliente->empresa_id         = $request->input('empresa_id_cliente_new_usuario_empresa');
            $cliente->nombres            = $request->input('nombres_cliente_new_usuaio_empresa');
            $cliente->ap_paterno         = $request->input('ap_paterno_cliente_new_usuaio_empresa');
            $cliente->ap_materno         = $request->input('ap_materno_cliente_new_usuaio_empresa');
            $cliente->cedula             = $request->input('cedula_cliente_new_usuaio_empresa');
            $cliente->complemento        = $request->input('complemento_cliente_new_usuaio_empresa');
            $cliente->nit                = $request->input('nit_cliente_new_usuaio_empresa');
            $cliente->razon_social       = $request->input('razon_social_cliente_new_usuaio_empresa');
            $cliente->correo             = $request->input('correo_cliente_new_usuaio_empresa');
            $cliente->numero_celular     = $request->input('num_ceular_cliente_new_usuaio_empresa');
            $cliente->save();

            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function listadoClientes(Request $request){
        return view('empresa.listadoClientes');
    }

    public function ajaxListadoClientesEmpresa(Request $request){
        if($request->ajax()){

            $usuario = Auth::user();
            $empresa_id = $usuario->empresa_id;

            $clientes = Cliente::where('empresa_id', $empresa_id)->get();

            $data['listado'] = view('empresa.ajaxListadoClientesEmpresa')->with(compact('clientes'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function guardarClienteEmpresaEmpresa(Request $request){
        if($request->ajax()){

            $suscripcion = app(SuscripcionController::class);
            $usuario     = Auth::user();
            $empresa     = $usuario->empresa;
            $empresa_id  = $usuario->empresa_id;

            $obtenerSuscripcionVigenteEmpresa = $suscripcion->obtenerSuscripcionVigenteEmpresa($empresa);

            if($obtenerSuscripcionVigenteEmpresa){

                $empresa_id = $usuario->empresa_id;
                $plan       = $obtenerSuscripcionVigenteEmpresa->plan;

                $cliente_id                  = $request->input('cliente_id_cliente_new_usuaio_empresa') ;

                if($suscripcion->verificarRegistroClienteByPlan($plan, $empresa) || $cliente_id != "0"){

                    // $cliente                     = $cliente_id == "0" ? new Cliente() : Cliente::find($cliente_id);
                    if($cliente_id == "0"){
                        $cliente                     = new Cliente();
                        $cliente->usuario_creador_id = $usuario->id;
                    }else{
                        $cliente                         = Cliente::find($cliente_id);
                        $cliente->usuario_modificador_id = $usuario->id;
                    }

                    $cliente->empresa_id         = $empresa_id;
                    $cliente->nombres            = $request->input('nombres_cliente_new_usuaio_empresa');
                    $cliente->ap_paterno         = $request->input('ap_paterno_cliente_new_usuaio_empresa');
                    $cliente->ap_materno         = $request->input('ap_materno_cliente_new_usuaio_empresa');
                    $cliente->cedula             = $request->input('cedula_cliente_new_usuaio_empresa');
                    $cliente->complemento        = $request->input('complemento_cliente_new_usuaio_empresa');
                    $cliente->nit                = $request->input('nit_cliente_new_usuaio_empresa');
                    $cliente->razon_social       = $request->input('razon_social_cliente_new_usuaio_empresa');
                    $cliente->correo             = $request->input('correo_cliente_new_usuaio_empresa');
                    $cliente->numero_celular     = $request->input('num_ceular_cliente_new_usuaio_empresa');
                    $cliente->save();

                    $data['estado'] = 'success';
                    $data['cliente']   = $cliente->id;
                }else{
                    $data['text']   = 'Alcanzo la cantidad maxima registros de clientes, solicite un plan superior.';
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = 'No existe suscripciones activas!, , solicite una suscripcion a un plan vigente.';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function listadoProductoServicioEmpresa(Request $request){
        $usuario    = Auth::user();
        $empresa_id = $usuario->empresa_id;
        $empresa    = $usuario->empresa;

        $documentos_sectores_asignados = $empresa->empresasDocumentos;

        // dd($documentos_sectores_asignados);

        $activiadesEconomica = SiatDependeActividades::where('empresa_id', $empresa_id)->get();
        $productoServicio    = SiatProductoServicio::where('empresa_id', $empresa_id)->get();
        $unidadMedida        = SiatUnidadMedida::all();

        return view('empresa.listadoProductoServicioEmpresa')->with(compact('activiadesEconomica', 'productoServicio','unidadMedida','documentos_sectores_asignados'));
    }

    public function ajaxListadoProductoServicioEmpresa(Request $request){
        if($request->ajax()){

            $usuario = Auth::user();
            $empresa_id = $usuario->empresa_id;

            $servicios = Servicio::where('empresa_id', $empresa_id)->get();

            $data['listado'] = view('empresa.ajaxListadoProductoServicioEmpresa')->with(compact('servicios'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function guardarProductoServicioEmpresa(Request $request){
        if($request->ajax()){

            $suscripcion = app(SuscripcionController::class);
            $usuario     = Auth::user();
            $empresa     = $usuario->empresa;

            $obtenerSuscripcionVigenteEmpresa = $suscripcion->obtenerSuscripcionVigenteEmpresa($empresa);

            if($obtenerSuscripcionVigenteEmpresa){
                $empresa_id = $usuario->empresa_id;
                $plan       = $obtenerSuscripcionVigenteEmpresa->plan;

                $guardarProductoServicioEmpresa = $request->input('servicio_producto_id_new_servicio');

                if($suscripcion->verificarRegistroServicioProductoByPlan($plan, $empresa) || $guardarProductoServicioEmpresa != "0"){

                    if($guardarProductoServicioEmpresa == "0"){
                        $servicio                     = new Servicio();
                        $servicio->usuario_creador_id = $usuario->id;
                    }else{
                        $servicio                         = Servicio::find($guardarProductoServicioEmpresa);
                        $servicio->usuario_modificador_id = $usuario->id;
                    }
                    // $servicio                              = $guardarProductoServicioEmpresa == "0" ? new Servicio()  : Servicio::find($guardarProductoServicioEmpresa);
                    $servicio->empresa_id                  = $empresa_id;
                    $servicio->siat_depende_actividades_id = $request->input('actividad_economica_siat_id_new_servicio');
                    $servicio->siat_documento_sector_id    = $request->input('documento_sector_siat_id_new_servicio');
                    $servicio->siat_producto_servicios_id  = $request->input('producto_servicio_siat_id_new_servicio');
                    $servicio->siat_unidad_medidas_id      = $request->input('unidad_medida_siat_id_new_servicio');
                    $servicio->numero_serie                = $request->input('numero_serie');
                    $servicio->codigo_imei                 = $request->input('codigo_imei');
                    $servicio->descripcion                 = $request->input('descrpcion_new_servicio');
                    $servicio->precio                      = $request->input('precio_new_servicio');
                    $servicio->save();

                    $data['estado'] = 'success';
                }else{
                    $data['text']   = 'Alcanzo la cantidad maxima registros de producto / servicio, solicite un plan superior.';
                    $data['estado'] = 'error';
                }

            }else{
                $data['text']   = 'No existe suscripciones activas!, , solicite una suscripcion a un plan vigente.';
                $data['estado'] = 'error';
            }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoAsignacionDocumentosSectores(Request $request){

        if($request->ajax()){

            $usuario                       = Auth::user();
            $empresa_id                    = $request->input('empresa');
            $documentos_sectores_asignados = EmpresaDocumentoSector::where('empresa_id', $empresa_id)
                                                                    ->get();

            $isAdmin = $usuario->isAdmin();

            $data['listado'] = view('empresa.ajaxListadoAsignacionDocumentosSectores')->with(compact('documentos_sectores_asignados', 'isAdmin'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardarAsignacionDocumentoSector(Request $request){
        if($request->ajax()){

            $empresaDocumentoSEctor                           = new EmpresaDocumentoSector();
            $empresaDocumentoSEctor->usuario_creador_id       = Auth::user()->id;
            $empresaDocumentoSEctor->empresa_id               = $request->input('new_asignacion_empresa_id');
            $empresaDocumentoSEctor->siat_documento_sector_id = $request->input('new_asignacion_documento_sector');
            $empresaDocumentoSEctor->save();

            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function eliminarCliente(Request $request){
        if($request->ajax()){
            $usuario    = Auth::user();
            $cliente_id = $request->input('cliente');
            $cliente    = Cliente::find($cliente_id);
            if($cliente){
                if($cliente->empresa_id == $usuario->empresa_id){
                    Cliente::destroy($cliente_id);
                    $data['text']   = 'El cliente se elimino con exito!';
                    $data['estado'] = 'success';
                }else{
                    $data['text']   = 'El cliente no pertenece a la empresa';
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = 'El cliente no existe';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function eliminarServicio(Request $request){
        if($request->ajax()){
            $usuario     = Auth::user();
            $servicio_id = $request->input('servicio');
            $servicio    = Servicio::find($servicio_id);
            if($servicio){
                if($servicio->empresa_id == $usuario->empresa_id){
                    Servicio::destroy($servicio_id);
                    $data['text']   = 'El servicio se elimino con exito!';
                    $data['estado'] = 'success';
                }else{
                    $data['text']   = 'El cliente no pertenece a la empresa';
                    $data['estado'] = 'error';
                }
            }else{
                $data['text']   = 'El servicio no existe';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function eliminarAsignaconDocumentoSector(Request $request){
        if($request->ajax()){
            $asignaicon_id = $request->input('asignacion');
            EmpresaDocumentoSector::destroy($asignaicon_id);
            $data['text']   = 'Se elimino con exito!';
            $data['estado'] = 'success';
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function detalleEmpresa(Request $request) {

        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        return view('empresa.detalleEmpresa')->with(compact('empresa'));
    }

    public function guardaEmpresa(Request $request){

        if($request->ajax()){

            // dd($request->all());

            $usuario = Auth::user();
            $empresa = $usuario->empresa;

            $empresa_id = $empresa->id;

            $empresa                         = Empresa::find($empresa_id);
            $empresa->usuario_modificador_id = Auth::user()->id;

            // $empresa->nombre                                = $request->input('nombre_empresa');
            // $empresa->nit                                   = $request->input('nit_empresa');
            // $empresa->razon_social                          = $request->input('razon_social');
            // $empresa->codigo_ambiente                       = $request->input('codigo_ambiente');
            // $empresa->codigo_modalidad                      = $request->input('codigo_modalidad');
            // $empresa->codigo_sistema                        = $request->input('codigo_sistema');
            // $empresa->codigo_documento_sector               = $request->input('documento_sectores');
            // $empresa->api_token                             = $request->input('api_token');
            // $empresa->url_facturacionCodigos                = $request->input('url_fac_codigos');
            // $empresa->url_facturacionSincronizacion         = $request->input('url_fac_sincronizacion');
            // $empresa->url_servicio_facturacion_compra_venta = $request->input('url_fac_servicios');
            // $empresa->url_facturacion_operaciones           = $request->input('url_fac_operaciones');
            // $empresa->municipio                             = $request->input('municipio');
            // $empresa->celular                               = $request->input('celular');
            $empresa->cafc                                  = $request->input('codigo_cafc');

            if($request->has('fila_archivo_p12')){
                // Obtén el archivo de la solicitud
                $file = $request->file('fila_archivo_p12');

                // Define el nombre del archivo y el directorio de almacenamiento
                $originalName = $file->getClientOriginalName();
                $filename     = time() . '_'. str_replace(' ', '_', $originalName);
                $directory    = 'assets/docs/certificate';

                // Guarda el archivo en el directorio especificado
                $file->move(public_path($directory), $filename);

                // Obtén la ruta completa del archivo
                $filePath = $directory . '/' . $filename;

                // Guarda la ruta del archivo en la base de datos
                $empresa->archivop12 = $filePath;

                if($request->input('contrasenia_archivo_p12') != null)
                    $empresa->contrasenia = $request->input('contrasenia_archivo_p12');

            }

            if($request->has('logo_empresa')){
                $foto = $request->file('logo_empresa');

                // Define el nombre del archivo y el directorio de almacenamiento
                $originalName = $foto->getClientOriginalName();
                $filename     = time() . '_'. str_replace(' ', '_', $originalName);
                $directory    = 'assets/img';

                // Guarda el archivo en el directorio especificado
                $foto->move(public_path($directory), $filename);

                // Obtén la ruta completa del archivo
                $filePath = $filename;

                // Guarda la ruta del archivo en la base de datos
                $empresa->logo = $filePath;

            }

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
}
