<?php

namespace App\Http\Controllers;

use App\Firma\Firmadores\FirmadorBoliviaSingle;
use App\Firma\Firmadores\FirmadorBoliviaSinglePemCrt;
use App\Models\Cliente;
use App\Models\Cuis;
use App\Models\Detalle;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\Servicio;
use App\Models\SiatDependeActividades;
use App\Models\SiatMotivoAnulacion;
use App\Models\SiatProductoServicio;
use App\Models\SiatTipoDocumentoIdentidad;
use App\Models\SiatTipoMetodoPagos;
use App\Models\SiatTipoMoneda;
use App\Models\SiatUnidadMedida;
use App\Models\Sucursal;
use App\Models\UrlApiServicioSiat;
use Carbon\Carbon;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Maatwebsite\Excel\Facades\Excel;
use PharData;
use SimpleXMLElement;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function formularioFacturacion(Request $request){

    //     $usuario = Auth::user();

    //     $empresa_id     = $usuario->empresa_id;
    //     $punto_venta_id = $usuario->punto_venta_id;
    //     $sucursal_id    = $usuario->sucursal_id;

    //     $empresa     = Empresa::find($empresa_id);
    //     $punto_venta = PuntoVenta::find($punto_venta_id);
    //     $sucursal    = Sucursal::find($sucursal_id);

    //     $url1   = $empresa->url_facturacionCodigos;
    //     $header = $empresa->api_token;

    //     // para el siat LA CONECCION
    //     $siat = app(SiatController::class);
    //     $verificacionSiat = json_decode($siat->verificarComunicacion(
    //         $url1,
    //         $header
    //     ));

    //     // dd(
    //     //     "sucursal_id ".$sucursal_id,
    //     //     "punto_venta_id ".$punto_venta_id,
    //     //     "codigo_ambiente ".$empresa->codigo_ambiente
    //     // );

    //     // SACAMOS EL CUIS VIGENTE
    //     $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

    //     // dd($cuis, $sucursal_id, $punto_venta_id, $empresa->codigo_ambiente, $usuario, $sucursal_id);


    //     // SACAMOS EL CUFD VIGENTE
    //     $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

    //     $servicios = Servicio::where('empresa_id', $empresa_id)
    //                         ->get();

    //     return view('factura.formularioFacturacion')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
    // }

    // public function formularioFacturacionTasaCero(Request $request){

    //     $usuario = Auth::user();

    //     $empresa_id     = $usuario->empresa_id;
    //     $punto_venta_id = $usuario->punto_venta_id;
    //     $sucursal_id    = $usuario->sucursal_id;

    //     $empresa     = Empresa::find($empresa_id);
    //     $punto_venta = PuntoVenta::find($punto_venta_id);
    //     $sucursal    = Sucursal::find($sucursal_id);

    //     $url1   = $empresa->url_facturacionCodigos;
    //     $header = $empresa->api_token;

    //     // para el siat LA CONECCION
    //     $siat = app(SiatController::class);
    //     $verificacionSiat = json_decode($siat->verificarComunicacion(
    //         $url1,
    //         $header
    //     ));

    //     // dd($verificacionSiat);

    //     // SACAMOS EL CUIS VIGENTE
    //     $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

    //     // SACAMOS EL CUFD VIGENTE
    //     $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

    //     $servicios = Servicio::where('empresa_id', $empresa_id)
    //                         ->get();

    //     return view('factura.formularioFacturacionTasaCero')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
    // }

    public function ajaxListadoClientes(Request $request){
        if($request->ajax()){

            $usuario = Auth::user();
            $empresa_id = $usuario->empresa_id;

            $clientes = Cliente::where('empresa_id', $empresa_id)
                                ->get();

            $data['listado'] = view('factura.ajaxListadoClientes')->with(compact('clientes'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function agregarProducto(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $servicio              = json_decode($request->input('serivicio_id_venta'));
            $precio_venta          = $request->input('precio_venta');
            $cantidad_venta        = $request->input('cantidad_venta');
            $total_venta           = $request->input('total_venta');
            $cliente_id_escogido   = $request->input('cliente_id_escogido');
            $descripcion_adicional = $request->input('descripcion_adicional');

            $servicioBuscado = Servicio::find($servicio->id);
            $servicioBuscado->numero_serie = $request->input('numero_serie');
            $servicioBuscado->codigo_imei = $request->input('codigo_imei');
            $servicioBuscado->save();

            // dd(Auth::user());

            $detalle                        = new Detalle();
            $detalle->usuario_creador_id    = Auth::user()->id;
            $detalle->empresa_id            = Auth::user()->empresa_id;
            $detalle->sucursal_id           = Auth::user()->sucursal_id;
            $detalle->punto_venta_id        = Auth::user()->punto_venta_id;
            $detalle->cliente_id            = $cliente_id_escogido;
            $detalle->servicio_id           = $servicio->id;
            $detalle->descripcion_adicional = $descripcion_adicional;
            $detalle->precio                = $precio_venta;
            $detalle->cantidad              = $cantidad_venta;
            $detalle->total                 = $total_venta;
            $detalle->descuento             = 0;
            $detalle->importe               = ($cantidad_venta*$precio_venta);
            $detalle->fecha                 = date('Y-m-d');
            $detalle->estado                = "Parapagar";
            $detalle->save();

            // dd($servicio->id, $request->all());


            // dd($request->all(), json_decode($request->input('serivicio_id_venta')), json_encode($request->input('serivicio_id_venta')));

            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoDetalles(Request $request){
        if($request->ajax()){

            $cliente_id     = $request->input('cliente');
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;
            $cliente        = Cliente::find($cliente_id);

            $nit          = $cliente->nit;
            $razon_social = $cliente->razon_social;

            $detalles = Detalle::where('cliente_id', $cliente_id)
                                ->where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->where('estado','Parapagar')
                                ->orderBy('id','desc')
                                ->get();

            // TIP DE DOCUMENTO
            $tipoDocumento = SiatTipoDocumentoIdentidad::all();

            // TIP METO DE PAGO
            $tipoMetodoPago = SiatTipoMetodoPagos::all();

            // TIPO MONEDA
            $tipoMonedas = SiatTipoMoneda::all();

            $data['listado']  = view('factura.ajaxListadoDetalles')->with(compact('detalles', 'tipoDocumento', 'tipoMetodoPago', 'tipoMonedas', 'nit', 'razon_social', 'empresa_id'))->render();
            $data['estado']   = 'success';
            $data['cantidad'] = count($detalles);

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function descuentoPorItem(Request $request){
        if($request->ajax()){

            $detalle_id = $request->input('detalle');
            $descunto   = $request->input('descunto');

            $detalle            = Detalle::find($detalle_id);
            $detalle->descuento = $descunto;
            $detalle->importe   = $detalle->total - $descunto;
            $detalle->save();

            $data['estado'] = 'success';
            $data['cliente'] = $detalle->cliente_id;

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function eliminarDetalle(Request $request){
        if($request->ajax()){
            $detalle = $request->input('detalle');
            Detalle::destroy($detalle);
            $data['estado'] = 'success';
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function descuentoAdicionalGlobal(Request $request){
        if($request->ajax()){

            $cliente_id     = $request->input('cliente');
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;

            // dd(
            //     "cliente_id > ".$cliente_id,
            //     "empresa_id > ".$empresa_id,
            //     "sucursal_id > ".$sucursal_id,
            //     "punto_venta_id > ".$punto_venta_id
            // );

            $detalles = Detalle::selectRaw('(SUM(total) - SUM(descuento)) as total_che')
                                ->where('empresa_id', $empresa_id)
                                ->where('cliente_id', $cliente_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->where('estado', 'Parapagar')
                                ->first();

            $data['estado'] = 'success';
            $data['valor'] = $detalles->total_che;


        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function verificaItemsGeneracion(Request $request) {

        if($request->ajax()){

            // dd($request->all());

            $cliente_id     = $request->input('cliente');
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;

            $detalles = Detalle::where('cliente_id', $cliente_id)
                                ->where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->where('estado', 'Parapagar')
                                // ->get();
                                ->count();
                                // ->toSql();

            // dd(
            //     "detalles => ".$detalles,
            //     "cliente_id => ".$cliente_id,
            //     "empresa_id => ".$empresa_id,
            //     "sucursal_id => ".$sucursal_id,
            //     "punto_venta_id => ".$punto_venta_id
            // );

            // $vehiculo_id = $request->input('vehiculo');

            // $pagos = Detalle::where('vehiculo_id',$vehiculo_id)
            //                 ->where('estado','Parapagar')
            //                 ->count();

            $data['estado']   = 'success';
            $data['cantidad'] = $detalles;

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;

    }

    public function arrayCuotasPagar(Request $request){
        if($request->ajax()){

            // $cliente = $request->input('cliente');

            $cliente_id     = $request->input('cliente');
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;


            // $datelles = Detalle::where('cliente_id', $cliente_id)
            //                     ->where('empresa_id', $empresa_id)
            //                     ->where('sucursal_id', $sucursal_id)
            //                     ->where('punto_venta_id', $punto_venta_id)
            //                     ->where('estado', 'Parapagar')
            //                     ->get();

            // dd(
            //     "cliente_id ".$cliente_id,
            //     "empresa_id ".$empresa_id,
            //     "sucursal_id ".$sucursal_id,
            //     "punto_venta_id ".$punto_venta_id
            // );

            $datelles = Detalle::select(
                                    'detalles.*',
                                    'siat_unidad_medidas.codigo_clasificador',
                                    'siat_producto_servicios.codigo_producto',
                                    'siat_depende_actividades.codigo_caeb',
                                    'servicios.descripcion',
                                    'servicios.numero_serie',
                                    'servicios.codigo_imei'
                                    )
                                ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
                                ->join('siat_depende_actividades', 'servicios.siat_depende_actividades_id', '=', 'siat_depende_actividades.id')
                                ->join('siat_producto_servicios', 'servicios.siat_producto_servicios_id', '=', 'siat_producto_servicios.id')
                                ->join('siat_unidad_medidas', 'servicios.siat_unidad_medidas_id', '=', 'siat_unidad_medidas.id')
                                ->where('detalles.cliente_id', $cliente_id)
                                ->where('detalles.empresa_id', $empresa_id)
                                ->where('detalles.sucursal_id', $sucursal_id)
                                ->where('detalles.punto_venta_id', $punto_venta_id)
                                ->where('detalles.estado', 'Parapagar')
                                ->get();

            // dd($datelles);

            // $servicios = Detalle::select('detalles.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
            //                     ->join('servicios', 'detalles.servicio_id','=', 'servicios.id')
            //                     ->where('detalles.estado',"paraPagar")
            //                     ->where('detalles.vehiculo_id',$cliente)
            //                     ->get();

            $detalles_ids =    $datelles->pluck('id');

            $data['lista']      = json_encode($datelles);
            $data['estado']     = 'success';
            $data['detalles']   = $detalles_ids;

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function numeroFactura($empresa_id, $sucursal_id, $punto_venta_id){
        // $numeroFactura = Factura::where('empresa_id', $empresa_id)
        //                         ->where('sucursal_id', $sucursal_id)
        //                         ->where('punto_venta_id', $punto_venta_id)
        //                         ->max('numero_factura');

        $numeroFactura = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->selectRaw('MAX(CAST(numero_factura AS UNSIGNED)) as numero_factura')
                                ->pluck('numero_factura')
                                ->first();

        return $numeroFactura;
    }

    public function numeroFacturaCafc($empresa_id, $sucursal_id, $punto_venta_id){
        // dd($empresa_id, $sucursal_id, $punto_venta_id);
        $numeroFactura = Factura::where('empresa_id', $empresa_id)
                        ->where('sucursal_id', $sucursal_id)
                        ->where('punto_venta_id', $punto_venta_id)
                        ->selectRaw('MAX(CAST(numero_cafc AS UNSIGNED)) as numero_cafc')
                        ->pluck('numero_cafc')
                        ->first();
                        // ->toSql();

                return $numeroFactura;
    }

    public function listado(Request $request){

        $siat_motivo_anulaciones = SiatMotivoAnulacion::all();

        return view('factura.listado')->with(compact('siat_motivo_anulaciones'));
    }

    public function ajaxListadoFacturas(Request $request){
        if($request->ajax()){

            // DE AQUI ESE EL ANTIGUO
            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;
            $empresa        = Auth::user()->empresa;

            // DE AQUI ESE EL ANTIGUO

            // $query = Factura::select('*')
            $query = Factura::select(
                            'facturas.numero_cafc',
                            'facturas.estado',
                            'facturas.codigo_descripcion',
                            'facturas.tipo_factura',
                            'facturas.uso_cafc',
                            'facturas.nit',
                            'facturas.cuf',
                            'facturas.id',
                            'facturas.fecha',
                            'facturas.total',
                            'facturas.numero_factura',
                            'facturas.empresa_id',
                            'facturas.siat_documento_sector_id',
                            'facturas.usuario_creador_id',

                            'clientes.cedula',
                            'clientes.nombres',
                            'clientes.ap_paterno',
                            'clientes.ap_materno',
                            )
                            ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
                            ->where('facturas.empresa_id', $empresa_id)
                            ->where('facturas.sucursal_id', $sucursal_id)
                            ->where('facturas.punto_venta_id', $punto_venta_id)

                            // ->whereNull('facturas.codigo_descripcion')
                            ;

            if(!is_null($request->input('buscar_nro_factura'))){
                $numero_factura = $request->input('buscar_nro_factura');
                $query->where('facturas.numero_factura', $numero_factura);
            }

            if(!is_null($request->input('buscar_nro_cedula'))){
                $cedula = $request->input('buscar_nro_cedula');
                $query->where('clientes.cedula', $cedula);
            }

            if(!is_null($request->input('buscar_nit'))){
                $nit = $request->input('buscar_nit');
                $query->where('facturas.nit', $nit);
            }

            if(!is_null($request->input('buscar_fecha_inicio')) && !is_null($request->input('buscar_fecha_fin'))){
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"]);
            }

            if(
                !is_null($request->input('buscar_nro_factura')) &&
                !is_null($request->input('buscar_nro_cedula')) &&
                !is_null($request->input('buscar_fecha_inicio')) &&
                !is_null($request->input('buscar_fecha_fin'))
            ){
                $facturas = $query->limit(500)->with('factura.empresa')->get();
            }else{
                $facturas = $query->orderBy('facturas.id', 'desc')->limit(100)->with('empresa')->get();
                // $facturas = $query->orderBy('facturas.id', 'desc')->with('empresa')->get();
            }

            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlVerificaFactura = $urlApiServicioSiat->getUrlVerificaFactura($empresa->codigo_ambiente);

            $url_verifica_factura = $UrlVerificaFactura->url_servicio;

            $data['listado'] = view('factura.ajaxListadoFacturas')->with(compact('facturas', 'url_verifica_factura'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function anularFactura(Request $request){
        if($request->ajax()){

            $factura_id       = $request->input('factura_id');
            $motivo_anulacion = $request->input('codigoMotivoAnulacion');

            $factura          = Factura::find($factura_id);
            $empresa_id       = $factura->empresa_id;
            $empresa          = Empresa::find($empresa_id);
            $sucursal         = Sucursal::find($factura->sucursal_id);
            $punto_venta      = PuntoVenta::find($factura->punto_venta_id);
            $cuis             = $empresa->cuisVigente($sucursal->id, $punto_venta->id, $empresa->codigo_ambiente);
            $documento_sector = $factura->siat_tipo_documento_sector;

            $siat       = app(SiatController::class);

            $cufdVigente = json_decode(
                $siat->verificarConeccion(
                    $empresa->id,
                    $sucursal->id,
                    $cuis->id,
                    $punto_venta->id,
                    $empresa->codigo_ambiente
                ));

            $urlApiServicioSiat = new UrlApiServicioSiat();
            if($documento_sector->codigo_clasificador == "8")
                $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionTasaCeroElectronica($empresa->codigo_ambiente, $empresa->codigo_modalidad);
            else
                $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionCompraVentaElctronica($empresa->codigo_ambiente, $empresa->codigo_modalidad);


            $header                = $empresa->api_token;
            $url3                  = $UrlSincronizacion->url_servicio;
            $codigoAmbiente        = $empresa->codigo_ambiente;
            $codigoDocumentoSector = $documento_sector->codigo_clasificador;
            $codigoModalidad       = $empresa->codigo_modalidad;
            $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
            $codigoSistema         = $empresa->codigo_sistema;
            $codigoSucursal        = $sucursal->codigo_sucursal;
            $scufd                 = $cufdVigente->codigo;
            $scuis                 = $cuis->codigo;
            $nit                   = $empresa->nit;
            $tipoFacturaDocumento  = ($documento_sector->codigo_clasificador == "8")? 2 : 1;

            $respuesta = json_decode($siat->anulacionFactura(
                $header,
                $url3,
                $codigoAmbiente,
                $codigoDocumentoSector,
                $codigoModalidad,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $scufd,
                $scuis,
                $nit,
                $tipoFacturaDocumento,

                $motivo_anulacion,
                $factura->cuf
            ));

            if($respuesta->estado == "success"){
                if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                    $factura->estado = 'Anulado';

                    // PARA ELIMINAR LOS DETALLES
                    Detalle::where('factura_id', $factura->id)->delete();

                    $cliente = Cliente::find($factura->cliente_id);

                    $correo = $cliente->correo;
                    $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                    $numero = $factura->numero;
                    $fecha  = $factura->fecha;

                    $this->enviaCorreoAnulacion($correo, $nombre, $numero, $fecha );

                    $data['estado'] = "success";
                }else{
                    $factura->descripcion = $respuesta->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                    $data['estado']       = "error";
                    $data['descripcion']  = $respuesta->resultado->RespuestaServicioFacturacion;
                    $data['msg']          = $respuesta->resultado;
                }
                $factura->save();
            }else{
                $data['text']                             = 'No existe';
                $data['estado']                           = 'error';
                $data['descripcion']['codigoDescripcion'] = 'Error al anular';
                $data['descripcion']['mensajesList']      = $respuesta;
                $data['msg']                              = $respuesta;
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function desanularFacturaAnulado(Request $request){
        if($request->ajax()){

            $factura_id = $request->input('factura');
            $factura    = Factura::find($factura_id);

            $empresa_id     = $factura->empresa_id;
            $sucursal_id    = $factura->sucursal_id;
            $punto_venta_id = $factura->punto_venta_id;

            $empresa          = Empresa::find($empresa_id);
            $sucursal         = Sucursal::find($sucursal_id);
            $punto_venta      = PuntoVenta::find($punto_venta_id);
            $cuis             = $empresa->cuisVigente($sucursal->id, $punto_venta->id, $empresa->codigo_ambiente);
            $documento_sector = $factura->siat_tipo_documento_sector;

            $siat       = app(SiatController::class);

            $cufdVigente = json_decode(
                $siat->verificarConeccion(
                    $empresa->id,
                    $sucursal->id,
                    $cuis->id,
                    $punto_venta->id,
                    $empresa->codigo_ambiente
                ));

            $urlApiServicioSiat = new UrlApiServicioSiat();
            if($documento_sector->codigo_clasificador == "8")
                $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionTasaCeroElectronica($empresa->codigo_ambiente, $empresa->codigo_modalidad);
            else
                $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionCompraVentaElctronica($empresa->codigo_ambiente, $empresa->codigo_modalidad);



            // dd($cufdVigente);

            // $header                = $empresa->api_token;
            // $url3                  = $empresa->url_servicio_facturacion_compra_venta;
            // $codigoAmbiente        = $empresa->codigo_ambiente;
            // $codigoDocumentoSector = $empresa->codigo_documento_sector;
            // $codigoModalidad       = $empresa->codigo_modalidad;
            // $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
            // $codigoSistema         = $empresa->codigo_sistema;
            // $codigoSucursal        = $sucursal->codigo_sucursal;
            // $scufd                 = $cufdVigente->codigo;
            // $scuis                 = $cuis->codigo;
            // $nit                   = $empresa->nit;


            $header                = $empresa->api_token;
            $url3                  = $UrlSincronizacion->url_servicio;
            $codigoAmbiente        = $empresa->codigo_ambiente;
            $codigoDocumentoSector = $documento_sector->codigo_clasificador;
            $codigoModalidad       = $empresa->codigo_modalidad;
            $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
            $codigoSistema         = $empresa->codigo_sistema;
            $codigoSucursal        = $sucursal->codigo_sucursal;
            $scufd                 = $cufdVigente->codigo;
            $scuis                 = $cuis->codigo;
            $nit                   = $empresa->nit;
            $cuf1                  = $factura->cuf;
            $tipoFacturaDocumento  = ($documento_sector->codigo_clasificador == "8")? 2 : 1;

            $respuesta = json_decode($siat->reversionAnulacionFactura(
                $header,
                $url3,
                $codigoAmbiente,
                $codigoDocumentoSector ,
                $codigoModalidad,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $scufd,
                $scuis,
                $nit,
                $cuf1,
                $tipoFacturaDocumento
            ));

            // dd($respuesta);

            if($respuesta->estado == "success"){
                if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                    $factura->estado = null;
                    Detalle::withTrashed()
                            ->where('factura_id', $factura->id)
                            ->update(['deleted_at' => null]);
                    $factura->save();
                    $data['estado'] = 'success';
                    $data['msg'] = $respuesta->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                }else{
                    $data['msg']   = $respuesta;
                    $data['estado'] = 'error';
                }
            }else{
                $data['msg']   = $respuesta;
                $data['estado'] = 'error';
            }
        }else{
            $data['msg']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sacaNumeroCafcUltimo(Request $request){
        if($request->ajax()){

            $usuario        = Auth::user();
            $empresa_id     = $usuario->empresa_id;
            $punto_venta_id = $usuario->punto_venta_id;
            $sucursal_id    = $usuario->sucursal_id;
            $empresa        = Empresa::find($empresa_id);

            $numero = $this->numeroFacturaCafc($empresa_id, $sucursal_id, $punto_venta_id);

            $data['numero'] = ($numero==null)? 1 : $numero + 1;
            $data['cafc']   = $empresa->cafc;
            $data['estado'] = 'success';

        }else{
            $data['msg']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function verificarNit(Request $request){
        if($request->ajax()){

            $nitVerificar = $request->input('nit');

            $usuario        = Auth::user();
            $empresa_id     = $usuario->empresa_id;
            $punto_venta_id = $usuario->punto_venta_id;
            $sucursal_id    = $usuario->sucursal_id;

            $empresa_objeto     = Empresa::find($empresa_id);
            $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
            $sucursal_objeto    = Sucursal::find($sucursal_id);

            $cuis_objeto       = Cuis::where('punto_venta_id', $punto_venta_objeto->id)
                              ->where('sucursal_id', $sucursal_objeto->id)
                              ->where('codigo_ambiente', $empresa_objeto->codigo_ambiente)
                              ->first();

            $urlApiServicioSiat = new UrlApiServicioSiat();
            $UrlSincronizacion  = $urlApiServicioSiat->getUrlCodigos($empresa_objeto->codigo_ambiente, $empresa_objeto->codigo_modalidad);

            $siat = app(SiatController::class);

            $verificarNit = json_decode($siat->verificarNit(
                $UrlSincronizacion->url_servicio,
                $empresa_objeto->api_token,
                $empresa_objeto->codigo_ambiente,
                $punto_venta_objeto->nombrePuntoVenta,
                $empresa_objeto->codigo_sistema,
                $sucursal_objeto->codigo_sucursal,
                $cuis_objeto->codigo,
                $empresa_objeto->nit,

                $nitVerificar
            ));

            if($verificarNit->estado == "success"){
                $data['msg']        = $verificarNit->resultado->RespuestaVerificarNit->mensajesList;
                $data['estado']     = 'success';
                $data['estadoSiat'] = $verificarNit->resultado->RespuestaVerificarNit->transaccion;
            }else{
                $data['msg']    = $verificarNit->resultado->RespuestaVerificarNit->mensajesList;
                $data['estado'] = 'success';
            }
        }else{
            $data['msg']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function formularioFacturacionCv(Request $request) {

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;

        $empresa     = Empresa::find($empresa_id);
        $punto_venta = PuntoVenta::find($punto_venta_id);
        $sucursal    = Sucursal::find($sucursal_id);

        $urlApiServicioSiat = new UrlApiServicioSiat();
        $UrlSincronizacion  = $urlApiServicioSiat->getUrlCodigos($empresa->codigo_ambiente, $empresa->codigo_modalidad);

        $url1   = $UrlSincronizacion->url_servicio;
        $header = $empresa->api_token;

        $siat_documento_sector_id = 33; //COMPRA Y VENTA

        // $documentoSector = 1;

        // para el siat LA CONECCION
        $siat = app(SiatController::class);
        $verificacionSiat = json_decode($siat->verificarComunicacion(
            $url1,
            $header
        ));

        // dd($verificacionSiat);

        // SACAMOS EL CUIS VIGENTE
        $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

        // SACAMOS EL CUFD VIGENTE
        $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

        $servicios = Servicio::where('empresa_id', $empresa_id)
                                ->where('siat_documento_sector_id', $siat_documento_sector_id)
                                ->get();

        // TIP DE DOCUMENTO
        $tipoDocumento = SiatTipoDocumentoIdentidad::all();

        // TIP METO DE PAGO
        $tipoMetodoPago = SiatTipoMetodoPagos::all();

        // TIPO MONEDA
        $tipoMonedas = SiatTipoMoneda::all();

        $documentos_sectores_asignados = $empresa->empresasDocumentos;
        $activiadesEconomica           = SiatDependeActividades::where('empresa_id', $empresa_id)->get();
        $productoServicio              = SiatProductoServicio::where('empresa_id', $empresa_id)->get();
        $unidadMedida                  = SiatUnidadMedida::all();

        return view('factura.formularioFacturacionCv')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa', 'tipoDocumento', 'tipoMetodoPago', 'tipoMonedas', 'documentos_sectores_asignados','activiadesEconomica','productoServicio','unidadMedida'));
    }

    public function  formularioFacturacionTc(Request $request){

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;

        $empresa     = Empresa::find($empresa_id);
        $punto_venta = PuntoVenta::find($punto_venta_id);
        $sucursal    = Sucursal::find($sucursal_id);

        $urlApiServicioSiat = new UrlApiServicioSiat();
        $UrlSincronizacion  = $urlApiServicioSiat->getUrlCodigos($empresa->codigo_ambiente, $empresa->codigo_modalidad);

        $url1   = $UrlSincronizacion->url_servicio;
        $header = $empresa->api_token;

        $siat_documento_sector_id = 10; //TASA CERO

        // para el siat LA CONECCION
        $siat = app(SiatController::class);
        $verificacionSiat = json_decode($siat->verificarComunicacion(
            $url1,
            $header
        ));

        // SACAMOS EL CUIS VIGENTE
        $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

        // SACAMOS EL CUFD VIGENTE
        $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

        $servicios = Servicio::where('empresa_id', $empresa_id)
                            ->where('siat_documento_sector_id',$siat_documento_sector_id)
                            ->get();

        // TIP DE DOCUMENTO
        $tipoDocumento = SiatTipoDocumentoIdentidad::all();

        // TIP METO DE PAGO
        $tipoMetodoPago = SiatTipoMetodoPagos::all();

        // TIPO MONEDA
        $tipoMonedas = SiatTipoMoneda::all();

        $documentos_sectores_asignados = $empresa->empresasDocumentos;
        $activiadesEconomica           = SiatDependeActividades::where('empresa_id', $empresa_id)->get();
        $productoServicio              = SiatProductoServicio::where('empresa_id', $empresa_id)->get();
        $unidadMedida                  = SiatUnidadMedida::all();

        return view('factura.formularioFacturacionTc')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa', 'tipoDocumento', 'tipoMetodoPago', 'tipoMonedas', 'documentos_sectores_asignados','activiadesEconomica','productoServicio','unidadMedida'));

    }

    public function ajaxListadoServicios(Request $request){

        if($request->ajax()){

            $usuario = Auth::user();
            $empresa_id = $usuario->empresa_id;

            $servicios = Servicio::where('empresa_id', $empresa_id)
                                ->get();

            $data['listado'] = view('factura.ajaxListadoServicios')->with(compact('servicios'))->render();
            $data['estado'] = 'success';

        }else{

            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function ajaxListadoClientesBusqueda(Request $request){
        if($request->ajax()){

            $empresa = Auth::user()->empresa;

            $query = Cliente::select('*')
                            ->where('empresa_id', $empresa->id);

            if(!is_null($request->input('cedula_escogido'))){
                $cedula = $request->input('cedula_escogido');
                $query->where('cedula', $cedula);
            }

            if(!is_null($request->input('nombre_escogido'))){
                $nombre = $request->input('nombre_escogido');
                $query->where('nombres', 'LIKE', "%$nombre%");
            }

            if(!is_null($request->input('ap_paterno_escogido'))){
                $paterno = $request->input('ap_paterno_escogido');
                $query->where('ap_paterno', 'LIKE', "%$paterno%");
            }

            if(!is_null($request->input('ap_materno_escogido'))){
                $materno = $request->input('ap_materno_escogido');
                $query->where('ap_materno', 'LIKE', "%$materno%");
            }

            if(
                !is_null($request->input('cedula_escogido')) &&
                !is_null($request->input('nombre_escogido')) &&
                !is_null($request->input('ap_paterno_escogido')) &&
                !is_null($request->input('ap_materno_escogido'))
            ){

                $clientes = $query->limit(5)->get();

            }else{
                $clientes = $query->orderBy('id', 'desc')->limit(10)->get();
            }

            // dd($clientes, $request->all());

            // $data['text']   = 'No existe';
            $data['estado'] = 'success';
            $data['cantidad'] = count($clientes);
            $data['listado'] = view('factura.ajaxListadoClientesBusqueda')->with(compact('clientes'))->render();

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function emitirFacturaCv(Request $request) {

        if($request->ajax()){

            $usuario        = Auth::user();
            $empresa        = $usuario->empresa;

            $suscripcion = app(SuscripcionController::class);

            $obtenerSuscripcionVigenteEmpresa = $suscripcion->obtenerSuscripcionVigenteEmpresa($empresa);

            if($obtenerSuscripcionVigenteEmpresa){

                $plan       = $obtenerSuscripcionVigenteEmpresa->plan;

                if($suscripcion->verificarRegistroFacturaByPlan($plan, $empresa, $obtenerSuscripcionVigenteEmpresa)){

                    $empresa_id     = $usuario->empresa_id;
                    $punto_venta_id = $usuario->punto_venta_id;
                    $sucursal_id    = $usuario->sucursal_id;

                    $empresa_objeto     = Empresa::find($empresa_id);
                    $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
                    $sucursal_objeto    = Sucursal::find($sucursal_id);

                    $cuis_objeto       = Cuis::where('punto_venta_id', $punto_venta_objeto->id)
                                            ->where('sucursal_id', $sucursal_objeto->id)
                                            ->where('codigo_ambiente', $empresa_objeto->codigo_ambiente)
                                            ->first();

                    $documento_sector = 1;

                    $carroVentas                        = $request->input('carrito');
                    $cliente_id                         = $request->input('cliente_id');
                    $facturacion_datos_tipo_metodo_pago = $request->input('facturacion_datos_tipo_metodo_pago');
                    $facturacion_datos_tipo_moneda      = $request->input('facturacion_datos_tipo_moneda');
                    $tipo_documento                     = $request->input('tipo_documento');
                    $nit_factura                        = $request->input('nit_factura');
                    $razon_factura                      = $request->input('razon_factura');
                    $tipo_facturacion                   = $request->input('tipo_facturacion');
                    $numero_factura_cafc                = $request->input('numero_factura_cafc');
                    $execpcion                          = $request->input('execpcion');
                    $complemento                        = $request->input('complemento');
                    $descuento_adicional                = $request->input('descuento_adicional');
                    $monto_total                        = $request->input('monto_total');
                    $uso_cafc                           = $request->input('uso_cafc');
                    $leyenda                            = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";

                    $contenidoabeceraFcv      = array();
                    $cabeceraFcv              = array();
                    $contenidoFacturaFcv      = array();
                    $contenidoDetalleFcv      = array();
                    $DetalleFcv               = array();
                    $contenidoFacturaPadreFcv = array();
                    $idDetalles               = array();

                    $contenidoabeceraFcv['nitEmisor']                    = $empresa_objeto->nit;
                    $contenidoabeceraFcv['razonSocialEmisor']            = $empresa_objeto->razon_social;
                    $contenidoabeceraFcv['municipio']                    = $empresa_objeto->municipio;
                    $contenidoabeceraFcv['telefono']                     = $empresa_objeto->celular;
                    $contenidoabeceraFcv['numeroFactura']                = null;
                    $contenidoabeceraFcv['cuf']                          = null;
                    $contenidoabeceraFcv['cufd']                         = null;
                    $contenidoabeceraFcv['codigoSucursal']               = $sucursal_objeto->codigo_sucursal;
                    $contenidoabeceraFcv['direccion']                    = null;
                    $contenidoabeceraFcv['codigoPuntoVenta']             = null;

                    // PARA LA HORA
                    $microtime                                                = microtime(true);
                    $seconds                                                  = floor($microtime);
                    $milliseconds                                             = round(($microtime - $seconds) * 1000);
                    $formattedDateTime                                        = date("Y-m-d\TH:i:s.") . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);

                    $contenidoabeceraFcv['fechaEmision']                 = $formattedDateTime;
                    $contenidoabeceraFcv['nombreRazonSocial']            = $razon_factura;
                    $contenidoabeceraFcv['codigoTipoDocumentoIdentidad'] = $tipo_documento;

                    $contenidoabeceraFcv['numeroDocumento']              = $nit_factura;
                    $contenidoabeceraFcv['complemento']                  = ($complemento != null && $complemento != '')? $complemento : null;
                    $contenidoabeceraFcv['codigoCliente']                = $cliente_id;
                    $contenidoabeceraFcv['codigoMetodoPago']             = $facturacion_datos_tipo_metodo_pago;
                    $contenidoabeceraFcv['numeroTarjeta']                = null;
                    $contenidoabeceraFcv['montoTotal']                   = $monto_total;
                    $contenidoabeceraFcv['montoTotalSujetoIva']          = $monto_total;
                    $contenidoabeceraFcv['codigoMoneda']                 = $facturacion_datos_tipo_moneda;
                    $contenidoabeceraFcv['tipoCambio']                   = 1;
                    $contenidoabeceraFcv['montoTotalMoneda']             = $monto_total;
                    $contenidoabeceraFcv['montoGiftCard']                = null;
                    $contenidoabeceraFcv['descuentoAdicional']           = $descuento_adicional;
                    $contenidoabeceraFcv['codigoExcepcion']              = ($execpcion === "true")? 1 : 0;
                    $contenidoabeceraFcv['cafc']                         = null;
                    $contenidoabeceraFcv['leyenda']                      = $leyenda;
                    $contenidoabeceraFcv['usuario']                      = $usuario->email;
                    $contenidoabeceraFcv['codigoDocumentoSector']        = $documento_sector;

                    $cabeceraFcv['cabecera'] = $contenidoabeceraFcv;

                    array_push($contenidoFacturaFcv, $cabeceraFcv);

                    // ----------------- AGREGAMOS EN L ATABLA DETALLES -----------------
                    foreach($carroVentas as $key => $item){

                        $detalle                        = new Detalle();
                        $detalle->usuario_creador_id    = $usuario->id;
                        $detalle->empresa_id            = $empresa_id;
                        $detalle->sucursal_id           = $sucursal_id;
                        $detalle->punto_venta_id        = $punto_venta_id;
                        $detalle->cliente_id            = $cliente_id;
                        $detalle->servicio_id           = $item['servicio_id'];
                        $detalle->descripcion_adicional = $item['descripcion_adicional'];
                        $detalle->numero_serie          = $item['numero_serie'];
                        $detalle->numero_imei           = $item['numero_imei'];
                        $detalle->precio                = $item['precio'];
                        $detalle->cantidad              = $item['cantidad'];
                        $detalle->total                 = $item['total'];
                        $detalle->descuento             = $item['descuento'];
                        $detalle->importe               = $item['subTotal'];
                        $detalle->fecha                 = date('Y-m-d H:i:s');
                        $detalle->estado                = 'Parapagar';
                        $detalle->save();

                        $servicio = Servicio::find($item['servicio_id']);

                        array_push($idDetalles, $detalle->id);

                        // ARMAMOS EL CONTENIDO DEL DETALLE
                        $contenidoDetalleFcv['actividadEconomica'] = $servicio->siatDependeActividad->codigo_caeb;
                        $contenidoDetalleFcv['codigoProductoSin']  = $servicio->siatProductoServicio->codigo_producto;
                        $contenidoDetalleFcv['codigoProducto']     = $servicio->id;
                        $contenidoDetalleFcv['descripcion']        = $servicio->descripcion."\n".$item['descripcion_adicional'];
                        $contenidoDetalleFcv['cantidad']           = $item['cantidad'];
                        $contenidoDetalleFcv['unidadMedida']       = $servicio->siatUnidadMedida->codigo_clasificador;
                        $contenidoDetalleFcv['precioUnitario']     = $item['precio'];
                        $contenidoDetalleFcv['montoDescuento']     = $item['descuento'];
                        $contenidoDetalleFcv['subTotal']           = $item['subTotal'];
                        $contenidoDetalleFcv['numeroSerie']        = $item['numero_serie'];
                        $contenidoDetalleFcv['numeroImei']         = $item['numero_imei'];

                        $DetalleFcv['detalle'] = $contenidoDetalleFcv;

                        array_push($contenidoFacturaFcv, $DetalleFcv);

                    }

                    $contenidoFacturaPadreFcv['factura'] = $contenidoFacturaFcv;
                    $datos                               = $contenidoFacturaPadreFcv;
                    $valoresCabecera                     = $datos['factura'][0]['cabecera'];
                    $puntoVenta                          = $punto_venta_objeto->codigoPuntoVenta;
                    $nitEmisorEmpresa                    = $empresa_objeto->nit;
                    $sucursalEmpresa                     = $sucursal_objeto->codigo_sucursal;

                    if($uso_cafc === "Si"){
                        $numeroFacturaEmpresa = $numero_factura_cafc;
                    }else{
                        $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
                        $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));
                    }

                    $nitEmisor          = str_pad($nitEmisorEmpresa,13,"0",STR_PAD_LEFT);
                    $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
                    $sucursal           = str_pad($sucursalEmpresa,4,"0",STR_PAD_LEFT);
                    $modalidad          = $empresa_objeto->codigo_modalidad;
                    $numeroFactura      = str_pad($numeroFacturaEmpresa,10,"0",STR_PAD_LEFT);

                    if($tipo_facturacion === "online"){
                        $tipoEmision        = 1;
                    }
                    else{
                        if($uso_cafc === "Si"){
                            $datos['factura'][0]['cabecera']['cafc']          = $empresa_objeto->cafc;
                            $datos['factura'][0]['cabecera']['numeroFactura'] = $numero_factura_cafc;
                        }
                        $tipoEmision = 2;
                    }

                    $tipoFactura        = ($documento_sector == 8)? 2 : 1; // Factura sin Derecho a Crédito Fiscal
                    $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
                    $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

                    $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

                    // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
                    $cliente        = Cliente::find($cliente_id);
                    $swFacturaEnvio = true;
                    if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                        // $data['estado'] = "error_email";
                        // $data['text']    = "La persona no tiene correo";
                        // return $data;
                        $swFacturaEnvio = false;
                    }

                    $cliente->nit              = $datos['factura'][0]['cabecera']['numeroDocumento'];
                    $cliente->razon_social     = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                    $cliente->save();

                    // CODIGO DE JOEL ESETE LO HIZMOMOS NOSOTROS
                    $cadenaConM11 = $cadena.$this->calculaDigitoMod11($cadena, 1, 9, false);
                    if($tipo_facturacion === "online"){

                        $siat = app(SiatController::class);

                        $cufdVigente = json_decode(
                            $siat->verificarConeccion(
                                $empresa_objeto->id,
                                $sucursal_objeto->id,
                                $cuis_objeto->id,
                                $punto_venta_objeto->id,
                                $empresa_objeto->codigo_ambiente
                            ));

                        // dd($cufdVigente);

                        $scufd                  = $cufdVigente->codigo;
                        $scodigoControl         = $cufdVigente->codigo_control;
                        $sdireccion             = $cufdVigente->direccion;
                        $sfechaVigenciaCufd     = $cufdVigente->fecha_vigencia;

                    }else{
                        $eventoSignificadoControlller = app(EventoSignificativoController::class);

                        $empresa_id      = $empresa_objeto->id;
                        $sucursal_id     = $sucursal_objeto->id;
                        $punto_venta_id  = $punto_venta_objeto->id;
                        $codigo_ambiente = $empresa_objeto->codigo_ambiente;

                        $datosCufdOffLine             = $eventoSignificadoControlller->sacarCufdVigenteFueraLinea(
                            $empresa_id,
                            $sucursal_id,
                            $punto_venta_id,
                            $codigo_ambiente
                        );

                        if($datosCufdOffLine['estado'] === "success"){
                            $scufd                  = $datosCufdOffLine['scufd'];
                            $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                            $sdireccion             = $datosCufdOffLine['sdireccion'];
                            $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                        }else{

                            $data['estado'] = "error";
                            $data['text']    = "ERROR AL RECUPERAR EL CUFD ANTIGUO";

                            return $data;
                        }
                    }

                    $cufPro                                                 = $this->generarBase16($cadenaConM11).$scodigoControl;

                    $datos['factura'][0]['cabecera']['numeroFactura']     = $numeroFacturaEmpresa;
                    $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
                    $datos['factura'][0]['cabecera']['cufd']                = $scufd;
                    $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
                    $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

                    $temporal = $datos['factura'];

                    if($empresa_objeto->codigo_modalidad == "1"){
                        $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                                <facturaElectronicaCompraVenta xsi:noNamespaceSchemaLocation="facturaElectronicaCompraVenta.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                                </facturaElectronicaCompraVenta>';
                    }else{
                        $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                                <facturaComputarizadaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd">
                                </facturaComputarizadaCompraVenta>';
                    }

                    $xml_temporal = new SimpleXMLElement($dar);
                    $this->formato_xml($temporal, $xml_temporal);

                    $nombreArchivo = $cufPro."_".$numeroFacturaEmpresa."_".$nitEmisorEmpresa;

                    $xml_temporal->asXML("assets/docs/facturaxml_$nombreArchivo.xml");

                    //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\
                    if($empresa_objeto->codigo_modalidad == "1"){

                        if(!is_null($empresa_objeto->archivop12)){
                            // dd($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                            $firmador = new FirmadorBoliviaSingle($empresa_objeto->archivop12, $empresa_objeto->contrasenia);

                            dd($firmador);

                            $xmlFirmado = $firmador->firmarRuta("assets/docs/facturaxml_$nombreArchivo.xml");
                            file_put_contents("assets/docs/facturaxml_$nombreArchivo.xml", $xmlFirmado);
                        }else{
                            $data['text']   = 'No existe el archivo .p12 de firmado';
                            $data['estado'] = 'error_firma';

                            return $data;
                        }
                    }
                    // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

                    // COMPRIMIMOS EL ARCHIVO A ZIP
                    $gzdato = gzencode(file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml",9));
                    $fiape = fopen("assets/docs/facturaxml_$nombreArchivo.xml.zip","w");
                    fwrite($fiape,$gzdato);
                    fclose($fiape);

                    //  hashArchivo EL ARCHIVO
                    $archivoZip = $gzdato;
                    $hashArchivo = hash("sha256", file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml"));

                    if($tipo_facturacion === "online"){

                        $urlApiServicioSiat = new UrlApiServicioSiat();
                        $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionCompraVentaElctronica($empresa_objeto->codigo_ambiente, $empresa_objeto->codigo_modalidad);

                        $header                 = $empresa_objeto->api_token;
                        $url3                   = $UrlSincronizacion->url_servicio;
                        $codigoAmbiente         = $empresa_objeto->codigo_ambiente;
                        $codigoDocumentoSector  = $documento_sector;
                        $codigoModalidad        = $empresa_objeto->codigo_modalidad;
                        $codigoPuntoVenta       = $punto_venta_objeto->codigoPuntoVenta;
                        $codigoSistema          = $empresa_objeto->codigo_sistema;
                        $codigoSucursal         = $sucursal_objeto->codigo_sucursal;
                        $scufd                  = $cufdVigente->codigo;
                        $scuis                  = $cuis_objeto->codigo;
                        $nit                    = $empresa_objeto->nit;
                        $tipoFacturaDocumento   = 1;

                        $siat = app(SiatController::class);
                        $for  = json_decode($siat->recepcionFactura(
                            $header,
                            $url3,
                            $codigoAmbiente,
                            $codigoDocumentoSector,
                            $codigoModalidad,
                            $codigoPuntoVenta,
                            $codigoSistema,
                            $codigoSucursal,
                            $scufd,
                            $scuis,
                            $nit,
                            $tipoFacturaDocumento,

                            $archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo)
                        );

                        // NUEVO CODIGO PARA EVITAR ERROES DE GENERACION DE FACTURAS Y EVITAR QUE SE CREE MAS FACTURAS ASI NOMAS
                        if($for->estado === "success"){

                            // $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                            if($for->resultado->RespuestaServicioFacturacion->transaccion){
                                $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;

                                $documentos_sector_model = $empresa_objeto->empresasDocumentosTipoSector($documento_sector);

                                // ESTO ES PARA LA FACTURA LA CREACION
                                $facturaVerdad                           = new Factura();
                                $facturaVerdad->usuario_creador_id       = Auth::user()->id;
                                $facturaVerdad->cliente_id               = $cliente->id;
                                $facturaVerdad->empresa_id               = $empresa_objeto->id;
                                $facturaVerdad->sucursal_id              = $sucursal_objeto->id;
                                $facturaVerdad->punto_venta_id           = $punto_venta_objeto->id;
                                $facturaVerdad->cufd_id                  = $cufdVigente->id;
                                $facturaVerdad->siat_documento_sector_id = $documentos_sector_model->id;
                                $facturaVerdad->fecha                    = $datos['factura'][0]['cabecera']['fechaEmision'];
                                $facturaVerdad->nit                      = $datos['factura'][0]['cabecera']['numeroDocumento'];
                                $facturaVerdad->razon_social             = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                                $facturaVerdad->numero_factura           = $numeroFacturaEmpresa;
                                $facturaVerdad->facturado                = "Si";
                                $facturaVerdad->total                    = $datos['factura'][0]['cabecera']['montoTotal'];
                                $facturaVerdad->monto_total_subjeto_iva  = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                                $facturaVerdad->descuento_adicional      = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                                $facturaVerdad->cuf                      = $datos['factura'][0]['cabecera']['cuf'];
                                $facturaVerdad->productos_xml            = file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml");
                                $facturaVerdad->codigo_descripcion       = $codigo_descripcion;
                                $facturaVerdad->codigo_recepcion         = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                                $facturaVerdad->codigo_transaccion       = $for->resultado->RespuestaServicioFacturacion->transaccion;
                                $facturaVerdad->descripcion              = NULL;
                                $facturaVerdad->uso_cafc                 = "No";
                                $facturaVerdad->registro_compra          = 'No';
                                $facturaVerdad->tipo_factura             = "online";
                                $facturaVerdad->save();

                                // AHORA AREMOS PARA LOS PAGOS
                                Detalle::whereIn('id', $idDetalles)
                                        ->update([
                                            'estado'     => 'Finalizado',
                                            'factura_id' => $facturaVerdad->id
                                        ]);

                                $data['estado'] = $codigo_descripcion;
                                $data['numero'] = $facturaVerdad->id;

                                // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                                if($swFacturaEnvio){
                                    $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                                    $this->enviaCorreo(
                                        $cliente->correo,
                                        $nombre,
                                        $facturaVerdad->numero,
                                        $facturaVerdad->fecha,
                                        $facturaVerdad->id
                                    );
                                }

                            }else{
                                $data['estado'] = "RECHAZADA";
                                $data['text'] = json_encode($for->resultado->RespuestaServicioFacturacion->mensajesList);
                            }
                        }else{
                            $data['estado'] = "RECHAZADA";
                            $data['text'] = $for->msg;
                        }
                    }else{

                        // ESTO ES PARA LA FACTURA LA CREACION
                        $documentos_sector_model = $empresa_objeto->empresasDocumentosTipoSector($documento_sector);

                        $facturaVerdad                          = new Factura();
                        $facturaVerdad->usuario_creador_id      = Auth::user()->id;
                        $facturaVerdad->cliente_id              = $cliente->id;
                        $facturaVerdad->empresa_id              = $empresa_objeto->id;
                        $facturaVerdad->sucursal_id             = $sucursal_objeto->id;
                        $facturaVerdad->punto_venta_id          = $punto_venta_objeto->id;
                        $facturaVerdad->cufd_id                 = $datosCufdOffLine['scufd_id'];
                        $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                        $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];
                        $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];

                        if($uso_cafc === "Si")
                            $facturaVerdad->numero_cafc          = $numeroFacturaEmpresa;
                        else
                            $facturaVerdad->numero_factura       = $numeroFacturaEmpresa;

                        $facturaVerdad->facturado                = "Si";
                        $facturaVerdad->total                    = $datos['factura'][0]['cabecera']['montoTotal'];
                        $facturaVerdad->monto_total_subjeto_iva  = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional      = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->cuf                      = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->productos_xml            = file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml");
                        $facturaVerdad->codigo_descripcion       = NULL;
                        $facturaVerdad->codigo_recepcion         = NULL;
                        $facturaVerdad->codigo_transaccion       = NULL;
                        $facturaVerdad->descripcion              = NULL;
                        $facturaVerdad->uso_cafc                 = ($uso_cafc === "Si")? "Si" : "No";
                        $facturaVerdad->tipo_factura             = "offline";
                        $facturaVerdad->registro_compra          = 'No';
                        $facturaVerdad->siat_documento_sector_id = $documentos_sector_model->id;

                        $facturaVerdad->save();

                        // AHORA AREMOS PARA LOS PAGOS
                        Detalle::whereIn('id', $idDetalles)
                        ->update([
                            'estado'     => 'Finalizado',
                            'factura_id' => $facturaVerdad->id
                        ]);

                        if($swFacturaEnvio){
                            // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                            $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                            $this->enviaCorreo(
                                $cliente->correo,
                                $nombre,
                                $facturaVerdad->numero,
                                $facturaVerdad->fecha,
                                $facturaVerdad->id
                            );
                        }

                        $data['estado']     = 'OFFLINE';
                    }

                    $archivo = "assets/docs/facturaxml_$nombreArchivo.xml";
                    $archivoZip = "assets/docs/facturaxml_$nombreArchivo.xml.zip";
                    // Verifica si el archivo existe antes de intentar eliminarlo
                    if (file_exists($archivo))
                        unlink($archivo);

                    if(file_exists($archivoZip))
                        unlink($archivoZip);

                }else{
                    $data['text']   = 'Alcanzo la cantidad maxima registros de facturas, solicite un plan superior.';
                    $data['estado'] = 'error_sus';
                }
            }else{
                $data['text']   = 'No existe suscripciones activas!, , solicite una suscripcion a un plan vigente.';
                $data['estado'] = 'error_sus';
            }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function emitirFacturaTc(Request $request){

        if($request->ajax()){

            $usuario        = Auth::user();
            $empresa        = $usuario->empresa;

            $suscripcion = app(SuscripcionController::class);

            $obtenerSuscripcionVigenteEmpresa = $suscripcion->obtenerSuscripcionVigenteEmpresa($empresa);

            if($obtenerSuscripcionVigenteEmpresa){

                $plan       = $obtenerSuscripcionVigenteEmpresa->plan;

                if($suscripcion->verificarRegistroFacturaByPlan($plan, $empresa, $obtenerSuscripcionVigenteEmpresa)){

                    $empresa_id     = $usuario->empresa_id;
                    $punto_venta_id = $usuario->punto_venta_id;
                    $sucursal_id    = $usuario->sucursal_id;

                    $empresa_objeto     = Empresa::find($empresa_id);
                    $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
                    $sucursal_objeto    = Sucursal::find($sucursal_id);

                    $cuis_objeto       = Cuis::where('punto_venta_id', $punto_venta_objeto->id)
                                    ->where('sucursal_id', $sucursal_objeto->id)
                                    ->where('codigo_ambiente', $empresa_objeto->codigo_ambiente)
                                    ->first();

                    $documento_sector = 8;

                    $carroVentas                        = $request->input('carrito');
                    $cliente_id                         = $request->input('cliente_id');
                    $facturacion_datos_tipo_metodo_pago = $request->input('facturacion_datos_tipo_metodo_pago');
                    $facturacion_datos_tipo_moneda      = $request->input('facturacion_datos_tipo_moneda');
                    $tipo_documento                     = $request->input('tipo_documento');
                    $nit_factura                        = $request->input('nit_factura');
                    $razon_factura                      = $request->input('razon_factura');
                    $tipo_facturacion                   = $request->input('tipo_facturacion');
                    $numero_factura_cafc                = $request->input('numero_factura_cafc');
                    $execpcion                          = $request->input('execpcion');
                    $complemento                        = $request->input('complemento');
                    $descuento_adicional                = $request->input('descuento_adicional');
                    $monto_total                        = $request->input('monto_total');
                    $uso_cafc                           = $request->input('uso_cafc');
                    $leyenda                            = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";

                    $contenidoabeceraFcv      = array();
                    $cabeceraFcv              = array();
                    $contenidoFacturaFcv      = array();
                    $contenidoDetalleFcv      = array();
                    $DetalleFcv               = array();
                    $contenidoFacturaPadreFcv = array();
                    $idDetalles               = array();

                    $contenidoabeceraFcv['nitEmisor']                    = $empresa_objeto->nit;
                    $contenidoabeceraFcv['razonSocialEmisor']            = $empresa_objeto->razon_social;
                    $contenidoabeceraFcv['municipio']                    = $empresa_objeto->municipio;
                    $contenidoabeceraFcv['telefono']                     = $empresa_objeto->celular;
                    $contenidoabeceraFcv['numeroFactura']                = null;
                    $contenidoabeceraFcv['cuf']                          = null;
                    $contenidoabeceraFcv['cufd']                         = null;
                    $contenidoabeceraFcv['codigoSucursal']               = $sucursal_objeto->codigo_sucursal;
                    $contenidoabeceraFcv['direccion']                    = null;
                    $contenidoabeceraFcv['codigoPuntoVenta']             = null;

                    // PARA LA HORA
                    $microtime                                                = microtime(true);
                    $seconds                                                  = floor($microtime);
                    $milliseconds                                             = round(($microtime - $seconds) * 1000);
                    $formattedDateTime                                        = date("Y-m-d\TH:i:s.") . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);

                    $contenidoabeceraFcv['fechaEmision']                 = $formattedDateTime;
                    $contenidoabeceraFcv['nombreRazonSocial']            = $razon_factura;
                    $contenidoabeceraFcv['codigoTipoDocumentoIdentidad'] = $tipo_documento;

                    $contenidoabeceraFcv['numeroDocumento']              = $nit_factura;
                    $contenidoabeceraFcv['complemento']                  = ($complemento != null && $complemento != '')? $complemento : null;
                    $contenidoabeceraFcv['codigoCliente']                = $cliente_id;
                    $contenidoabeceraFcv['codigoMetodoPago']             = $facturacion_datos_tipo_metodo_pago;
                    $contenidoabeceraFcv['numeroTarjeta']                = null;
                    $contenidoabeceraFcv['montoTotal']                   = $monto_total;
                    $contenidoabeceraFcv['montoTotalSujetoIva']          = 0; //PARA ESTE SECTOR ENVIAR 0
                    $contenidoabeceraFcv['codigoMoneda']                 = $facturacion_datos_tipo_moneda;
                    $contenidoabeceraFcv['tipoCambio']                   = 1;
                    $contenidoabeceraFcv['montoTotalMoneda']             = $monto_total;
                    $contenidoabeceraFcv['montoGiftCard']                = null;
                    $contenidoabeceraFcv['descuentoAdicional']           = $descuento_adicional;
                    $contenidoabeceraFcv['codigoExcepcion']              = ($execpcion === "true")? 1 : 0;
                    $contenidoabeceraFcv['cafc']                         = null;
                    $contenidoabeceraFcv['leyenda']                      = $leyenda;
                    $contenidoabeceraFcv['usuario']                      = $usuario->email;
                    $contenidoabeceraFcv['codigoDocumentoSector']        = $documento_sector;

                    $cabeceraFcv['cabecera'] = $contenidoabeceraFcv;

                    array_push($contenidoFacturaFcv, $cabeceraFcv);

                    // ----------------- AGREGAMOS EN L ATABLA DETALLES -----------------
                    foreach($carroVentas as $key => $item){

                        $detalle                        = new Detalle();
                        $detalle->usuario_creador_id    = $usuario->id;
                        $detalle->empresa_id            = $empresa_id;
                        $detalle->sucursal_id           = $sucursal_id;
                        $detalle->punto_venta_id        = $punto_venta_id;
                        $detalle->cliente_id            = $cliente_id;
                        $detalle->servicio_id           = $item['servicio_id'];
                        $detalle->descripcion_adicional = $item['descripcion_adicional'];
                        // $detalle->numero_serie          = $item['numero_serie'];
                        // $detalle->numero_imei           = $item['numero_imei'];
                        $detalle->precio                = $item['precio'];
                        $detalle->cantidad              = $item['cantidad'];
                        $detalle->total                 = $item['total'];
                        $detalle->descuento             = $item['descuento'];
                        $detalle->importe               = $item['subTotal'];
                        $detalle->fecha                 = date('Y-m-d H:i:s');
                        $detalle->estado                = 'Parapagar';
                        $detalle->save();

                        $servicio = Servicio::find($item['servicio_id']);

                        array_push($idDetalles, $detalle->id);

                        // ARMAMOS EL CONTENIDO DEL DETALLE
                        $contenidoDetalleFcv['actividadEconomica'] = $servicio->siatDependeActividad->codigo_caeb;
                        $contenidoDetalleFcv['codigoProductoSin']  = $servicio->siatProductoServicio->codigo_producto;
                        $contenidoDetalleFcv['codigoProducto']     = $servicio->id;
                        $contenidoDetalleFcv['descripcion']        = $servicio->descripcion."\n".$item['descripcion_adicional'];
                        $contenidoDetalleFcv['cantidad']           = $item['cantidad'];
                        $contenidoDetalleFcv['unidadMedida']       = $servicio->siatUnidadMedida->codigo_clasificador;
                        $contenidoDetalleFcv['precioUnitario']     = $item['precio'];
                        $contenidoDetalleFcv['montoDescuento']     = $item['descuento'];
                        $contenidoDetalleFcv['subTotal']           = $item['subTotal'];
                        // $contenidoDetalleFcv['numeroSerie']        = $item['numero_serie'];
                        // $contenidoDetalleFcv['numeroImei']         = $item['numero_imei'];

                        $DetalleFcv['detalle'] = $contenidoDetalleFcv;

                        array_push($contenidoFacturaFcv, $DetalleFcv);

                    }

                    $contenidoFacturaPadreFcv['factura'] = $contenidoFacturaFcv;

                    $datos           = $contenidoFacturaPadreFcv;
                    $valoresCabecera = $datos['factura'][0]['cabecera'];
                    $puntoVenta      = $punto_venta_objeto->codigoPuntoVenta;

                    $nitEmisorEmpresa     = $empresa_objeto->nit;
                    $sucursalEmpresa      = $sucursal_objeto->codigo_sucursal;

                    if($uso_cafc === "Si"){
                        $numeroFacturaEmpresa = $numero_factura_cafc;
                    }else{
                        $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
                        $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));
                    }

                    $nitEmisor          = str_pad($nitEmisorEmpresa,13,"0",STR_PAD_LEFT);
                    $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
                    $sucursal           = str_pad($sucursalEmpresa,4,"0",STR_PAD_LEFT);
                    $modalidad          = $empresa_objeto->codigo_modalidad;
                    $numeroFactura      = str_pad($numeroFacturaEmpresa,10,"0",STR_PAD_LEFT);

                    if($tipo_facturacion === "online"){
                        $tipoEmision        = 1;
                    }
                    else{
                        if($uso_cafc === "Si"){
                            $datos['factura'][0]['cabecera']['cafc']          = $empresa_objeto->cafc;
                            $datos['factura'][0]['cabecera']['numeroFactura'] = $numero_factura_cafc;
                        }
                        $tipoEmision = 2;
                    }

                    $tipoFactura        = ($documento_sector == 8)? 2 : 1; // Factura sin Derecho a Crédito Fiscal
                    $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
                    $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

                    $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

                    // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
                    $cliente = Cliente::find($cliente_id);
                    $swFacturaEnvio = true;
                    if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                        // $data['estado'] = "error_email";
                        // $data['text']    = "La persona no tiene correo";
                        // return $data;
                        $swFacturaEnvio = false;
                    }
                    $cliente->nit              = $datos['factura'][0]['cabecera']['numeroDocumento'];
                    $cliente->razon_social     = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                    $cliente->save();

                    // CODIGO DE JOEL ESETE LO HIZMOMOS NOSOTROS
                    $cadenaConM11 = $cadena.$this->calculaDigitoMod11($cadena, 1, 9, false);
                    if($tipo_facturacion === "online"){

                        $siat = app(SiatController::class);

                        $cufdVigente = json_decode(
                            $siat->verificarConeccion(
                                $empresa_objeto->id,
                                $sucursal_objeto->id,
                                $cuis_objeto->id,
                                $punto_venta_objeto->id,
                                $empresa_objeto->codigo_ambiente
                            ));

                        $scufd                  = $cufdVigente->codigo;
                        $scodigoControl         = $cufdVigente->codigo_control;
                        $sdireccion             = $cufdVigente->direccion;
                        $sfechaVigenciaCufd     = $cufdVigente->fecha_vigencia;

                    }else{
                        $eventoSignificadoControlller = app(EventoSignificativoController::class);

                        $empresa_id      = $empresa_objeto->id;
                        $sucursal_id     = $sucursal_objeto->id;
                        $punto_venta_id  = $punto_venta_objeto->id;
                        $codigo_ambiente = $empresa_objeto->codigo_ambiente;

                        $datosCufdOffLine             = $eventoSignificadoControlller->sacarCufdVigenteFueraLinea(
                            $empresa_id,
                            $sucursal_id,
                            $punto_venta_id,
                            $codigo_ambiente
                        );

                        if($datosCufdOffLine['estado'] === "success"){
                            $scufd                  = $datosCufdOffLine['scufd'];
                            $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                            $sdireccion             = $datosCufdOffLine['sdireccion'];
                            $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                        }else{

                            $data['estado'] = "error";
                            $data['text']    = "ERROR AL RECUPERAR EL CUFD ANTIGUO";

                            return $data;
                        }
                    }

                    $cufPro                                                 = $this->generarBase16($cadenaConM11).$scodigoControl;

                    $datos['factura'][0]['cabecera']['numeroFactura']     = $numeroFacturaEmpresa;
                    $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
                    $datos['factura'][0]['cabecera']['cufd']                = $scufd;
                    $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
                    $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

                    $temporal = $datos['factura'];

                    if($empresa_objeto->codigo_modalidad == "1"){
                        $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                                <facturaElectronicaTasaCero xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaElectronicaTasaCero.xsd">
                                </facturaElectronicaTasaCero>';
                    }else{
                        $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                                <facturaComputarizadaTasaCero xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaComputarizadaTasaCero.xsd">
                                </facturaComputarizadaTasaCero>';
                    }

                    $xml_temporal = new SimpleXMLElement($dar);
                    $this->formato_xml($temporal, $xml_temporal);

                    $nombreArchivo = $cufPro."_".$numeroFacturaEmpresa."_".$nitEmisorEmpresa;

                    $xml_temporal->asXML("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml");

                    //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\
                    if($empresa_objeto->codigo_modalidad == "1"){
                        if(!is_null($empresa_objeto->archivop12)){
                            // dd($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                            $firmador = new FirmadorBoliviaSingle($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                            $xmlFirmado = $firmador->firmarRuta("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml");
                            file_put_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml", $xmlFirmado);
                        }else{
                            $data['text']   = 'No existe el archivo .p12 de firmado';
                            $data['estado'] = 'error_firma';

                            return $data;
                        }
                    }
                    // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

                    // COMPRIMIMOS EL ARCHIVO A ZIP
                    $gzdato = gzencode(file_get_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml",9));
                    $fiape = fopen("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml.zip","w");
                    fwrite($fiape,$gzdato);
                    fclose($fiape);

                    //  hashArchivo EL ARCHIVO
                    $archivoZip = $gzdato;
                    $hashArchivo = hash("sha256", file_get_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml"));

                    if($tipo_facturacion === "online"){

                        $urlApiServicioSiat = new UrlApiServicioSiat();
                        $UrlSincronizacion  = $urlApiServicioSiat->getUrlFacturacionTasaCeroElectronica($empresa_objeto->codigo_ambiente, $empresa_objeto->codigo_modalidad);

                        $header                 = $empresa_objeto->api_token;
                        $url3                   = $UrlSincronizacion->url_servicio;
                        $codigoAmbiente         = $empresa_objeto->codigo_ambiente;
                        $codigoDocumentoSector  = $documento_sector;
                        $codigoModalidad        = $empresa_objeto->codigo_modalidad;
                        $codigoPuntoVenta       = $punto_venta_objeto->codigoPuntoVenta;
                        $codigoSistema          = $empresa_objeto->codigo_sistema;
                        $codigoSucursal         = $sucursal_objeto->codigo_sucursal;
                        $scufd                  = $cufdVigente->codigo;
                        $scuis                  = $cuis_objeto->codigo;
                        $nit                    = $empresa_objeto->nit;
                        $tipoFacturaDocumento   = 2;

                        $siat = app(SiatController::class);
                        $for  = json_decode($siat->recepcionFactura(
                            $header,
                            $url3,
                            $codigoAmbiente,
                            $codigoDocumentoSector,
                            $codigoModalidad,
                            $codigoPuntoVenta,
                            $codigoSistema,
                            $codigoSucursal,
                            $scufd,
                            $scuis,
                            $nit,
                            $tipoFacturaDocumento,

                            $archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo)
                        );

                        // NUEVO CODIGO PARA EVITAR ERROES DE GENERACION DE FACTURAS Y EVITAR QUE SE CREE MAS FACTURAS ASI NOMAS

                        if($for->estado === "success"){

                            // $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                            if($for->resultado->RespuestaServicioFacturacion->transaccion){
                                $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;

                                $documentos_sector_model = $empresa_objeto->empresasDocumentosTipoSector($documento_sector);

                                // ESTO ES PARA LA FACTURA LA CREACION
                                $facturaVerdad                           = new Factura();
                                $facturaVerdad->usuario_creador_id       = Auth::user()->id;
                                $facturaVerdad->cliente_id               = $cliente->id;
                                $facturaVerdad->empresa_id               = $empresa_objeto->id;
                                $facturaVerdad->sucursal_id              = $sucursal_objeto->id;
                                $facturaVerdad->punto_venta_id           = $punto_venta_objeto->id;
                                $facturaVerdad->cufd_id                  = $cufdVigente->id;
                                $facturaVerdad->siat_documento_sector_id = $documentos_sector_model->id;
                                $facturaVerdad->fecha                    = $datos['factura'][0]['cabecera']['fechaEmision'];
                                $facturaVerdad->nit                      = $datos['factura'][0]['cabecera']['numeroDocumento'];
                                $facturaVerdad->razon_social             = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                                $facturaVerdad->numero_factura           = $numeroFacturaEmpresa;
                                $facturaVerdad->facturado                = "Si";
                                $facturaVerdad->total                    = $datos['factura'][0]['cabecera']['montoTotal'];
                                $facturaVerdad->monto_total_subjeto_iva  = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                                $facturaVerdad->descuento_adicional      = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                                $facturaVerdad->cuf                      = $datos['factura'][0]['cabecera']['cuf'];
                                $facturaVerdad->productos_xml            = file_get_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml");
                                $facturaVerdad->codigo_descripcion       = $codigo_descripcion;
                                $facturaVerdad->codigo_recepcion         = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                                $facturaVerdad->codigo_transaccion       = $for->resultado->RespuestaServicioFacturacion->transaccion;
                                $facturaVerdad->descripcion              = NULL;
                                $facturaVerdad->uso_cafc                 = "No";
                                $facturaVerdad->registro_compra          = 'No';
                                $facturaVerdad->tipo_factura             = "online";

                                $facturaVerdad->save();

                                // AHORA AREMOS PARA LOS PAGOS
                                Detalle::whereIn('id', $idDetalles)
                                        ->update([
                                            'estado'     => 'Finalizado',
                                            'factura_id' => $facturaVerdad->id
                                        ]);

                                $data['estado'] = $codigo_descripcion;
                                $data['numero'] = $facturaVerdad->id;

                                // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                                if($swFacturaEnvio){
                                    $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                                    $this->enviaCorreo(
                                        $cliente->correo,
                                        $nombre,
                                        $facturaVerdad->numero,
                                        $facturaVerdad->fecha,
                                        $facturaVerdad->id
                                    );
                                }

                            }else{
                                $data['estado'] = "RECHAZADA";
                                $data['text'] = json_encode($for->resultado->RespuestaServicioFacturacion->mensajesList);
                            }
                        }else{
                            $data['estado'] = "RECHAZADA";
                            $data['text'] = $for->msg;
                        }
                    }else{

                        $documentos_sector_model = $empresa_objeto->empresasDocumentosTipoSector($documento_sector);

                        // ESTO ES PARA LA FACTURA LA CREACION
                        $facturaVerdad                           = new Factura();
                        $facturaVerdad->usuario_creador_id       = Auth::user()->id;
                        $facturaVerdad->cliente_id               = $cliente->id;
                        $facturaVerdad->empresa_id               = $empresa_objeto->id;
                        $facturaVerdad->sucursal_id              = $sucursal_objeto->id;
                        $facturaVerdad->punto_venta_id           = $punto_venta_objeto->id;
                        $facturaVerdad->cufd_id                  = $datosCufdOffLine['scufd_id'];
                        $facturaVerdad->siat_documento_sector_id = $documentos_sector_model->id;
                        $facturaVerdad->fecha                    = $datos['factura'][0]['cabecera']['fechaEmision'];
                        $facturaVerdad->nit                      = $datos['factura'][0]['cabecera']['numeroDocumento'];
                        $facturaVerdad->razon_social             = $datos['factura'][0]['cabecera']['nombreRazonSocial'];

                        if($uso_cafc === "Si")
                            $facturaVerdad->numero_cafc          = $numeroFacturaEmpresa;
                        else
                            $facturaVerdad->numero_factura       = $numeroFacturaEmpresa;

                        $facturaVerdad->facturado               = "Si";
                        $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                        $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->productos_xml           = file_get_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml");
                        $facturaVerdad->codigo_descripcion      = NULL;
                        $facturaVerdad->codigo_recepcion        = NULL;
                        $facturaVerdad->codigo_transaccion      = NULL;
                        $facturaVerdad->descripcion             = NULL;
                        $facturaVerdad->uso_cafc                = ($uso_cafc === "Si")? "Si" : "No";
                        $facturaVerdad->tipo_factura            = "offline";
                        $facturaVerdad->registro_compra         = 'No';


                        $facturaVerdad->save();

                        // AHORA AREMOS PARA LOS PAGOS
                        Detalle::whereIn('id', $idDetalles)
                        ->update([
                            'estado'     => 'Finalizado',
                            'factura_id' => $facturaVerdad->id
                        ]);

                        if($swFacturaEnvio){
                            // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                            $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                            $this->enviaCorreo(
                                $cliente->correo,
                                $nombre,
                                $facturaVerdad->numero,
                                $facturaVerdad->fecha,
                                $facturaVerdad->id
                            );
                        }

                        $data['estado']     = 'OFFLINE';
                    }

                    $archivo = "assets/docs/facturaxmlTasaCero_$nombreArchivo.xml";
                    $archivoZip = "assets/docs/facturaxmlTasaCero_$nombreArchivo.xml.zip";
                    // Verifica si el archivo existe antes de intentar eliminarlo
                    if (file_exists($archivo))
                        unlink($archivo);

                    if(file_exists($archivoZip))
                        unlink($archivoZip);
                }else{
                    $data['text']   = 'Alcanzo la cantidad maxima registros de facturas, solicite un plan superior.';
                    $data['estado'] = 'error_sus';
                }
            }else{
                $data['text']   = 'No existe suscripciones activas!, , solicite una suscripcion a un plan vigente.';
                $data['estado'] = 'error_sus';
            }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;

    }

    public function generaPdfFacturaNewCv(Request $request, $factura_id){

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;
        $empresa        = $usuario->empresa;

        $factura = Factura::find($factura_id);

        if($factura){
            if($factura->empresa_id == $empresa_id){
                $xml     = $factura['productos_xml'];

                $archivoXML = new SimpleXMLElement($xml);

                $cabeza = (array) $archivoXML;

                $cuf            = (string)$cabeza['cabecera']->cuf;
                $numeroFactura  = (string)$cabeza['cabecera']->numeroFactura;


                $urlApiServicioSiat = new UrlApiServicioSiat();
                $UrlVerificaFactura = $urlApiServicioSiat->getUrlVerificaFactura($empresa->codigo_ambiente);

                // Genera el texto para el código QR
                // $textoQR = $factura->empresa->url_verifica."?nit=".$empresa->nit."&cuf=".$factura->cuf."&numero=".$numeroFactura."&t=2";
                $textoQR = $UrlVerificaFactura->url_servicio."?nit=".$empresa->nit."&cuf=".$factura->cuf."&numero=".$numeroFactura."&t=2";
                // Genera la ruta temporal para guardar la imagen del código QR
                $rutaImagenQR = storage_path('app/public/qr_code.png');
                // Genera el código QR y guarda la imagen en la ruta temporal
                QrCode::generate($textoQR, $rutaImagenQR);

                $pdf = PDF::loadView('factura.pdf.generaPdfFacturaNewCv', compact('factura', 'archivoXML','rutaImagenQR', 'empresa'))->setPaper('letter');

                return $pdf->stream('facturaCv.pdf');
            }else{
                throw new AuthorizationException();
            }
        }else{
            throw new NotFoundHttpException();
        }
    }

    public function imprimeFactura(Request $request, $factura_id){

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;
        $empresa        = $usuario->empresa;

        $factura = Factura::find($factura_id);

        if($factura){
            if($factura->empresa_id == $empresa_id){

                $xml           = $factura['productos_xml'];
                $archivoXML    = new SimpleXMLElement($xml);
                $cabeza        = (array) $archivoXML;
                $cuf           = (string)$cabeza['cabecera']->cuf;
                $numeroFactura = (string)$cabeza['cabecera']->numeroFactura;

                $urlApiServicioSiat = new UrlApiServicioSiat();
                $UrlVerificaFactura = $urlApiServicioSiat->getUrlVerificaFactura($empresa->codigo_ambiente);

                // $textoQR = $factura->empresa->url_verifica."?nit=".$empresa->nit."&cuf=".$factura->cuf."&numero=".$numeroFactura."&t=1";
                $textoQR = $UrlVerificaFactura->url_servicio."?nit=".$empresa->nit."&cuf=".$factura->cuf."&numero=".$numeroFactura."&t=1";

                // Genera la ruta temporal para guardar la imagen del código QR
                $rutaImagenQR = storage_path('app/public/qr_code.png');
                $urlImagenQR = asset(str_replace(storage_path('app/public'), 'storage', $rutaImagenQR));
                // Genera el código QR y guarda la imagen en la ruta temporal
                QrCode::generate($textoQR, $rutaImagenQR);
                // QrCode::format('png')->generate($textoQR, $rutaImagenQR);

                return view('factura.pdf.imprimeFactura')->with(compact('factura', 'archivoXML', 'cabeza', 'empresa'));

            }else{
                throw new AuthorizationException();
            }
        }else{
            throw new NotFoundHttpException();
        }

    }

    public function reportePDF(Request $request){

        if($request->ajax()){

            // DE AQUI ESE EL ANTIGUO
            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;
            $empresa        = Auth::user()->empresa;

            $query = Factura::select(
                'facturas.numero_cafc',
                'facturas.estado',
                'facturas.codigo_descripcion',
                'facturas.tipo_factura',
                'facturas.uso_cafc',
                'facturas.nit',
                'facturas.cuf',
                'facturas.id',
                'facturas.fecha',
                'facturas.total',
                'facturas.razon_social',
                'facturas.numero_factura',
                'facturas.empresa_id',
                'facturas.siat_documento_sector_id',
                'facturas.usuario_creador_id',

                'clientes.cedula',
                'clientes.nombres',
                'clientes.ap_paterno',
                'clientes.ap_materno',
                )
            ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
            ->where('facturas.empresa_id', $empresa_id)
            ->where('facturas.sucursal_id', $sucursal_id)
            ->where('facturas.punto_venta_id', $punto_venta_id)
            ;

            if(!is_null($request->input('buscar_nro_factura'))){
                $numero_factura = $request->input('buscar_nro_factura');
                $query->where('facturas.numero_factura', $numero_factura);
            }

            if(!is_null($request->input('buscar_nro_cedula'))){
                $cedula = $request->input('buscar_nro_cedula');
                $query->where('clientes.cedula', $cedula);
            }

            if(!is_null($request->input('buscar_nit'))){
                $nit = $request->input('buscar_nit');
                $query->where('facturas.nit', $nit);
            }

            if(!is_null($request->input('buscar_fecha_inicio')) && !is_null($request->input('buscar_fecha_fin'))){
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"]);
            }

            $facturas = $query->get();

            // Generar el PDF, configurando la orientación y el tamaño
            $pdf = PDF::loadView('factura.pdf.reportePDF', compact('facturas','empresa'))
            ->setPaper('letter', 'landscape'); // 'letter' es tamaño carta, 'landscape' es horizontal

            // Forzar la descarga del PDF con un nombre específico
            return $pdf->download('reporte_facturas.pdf');

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;

    }

    public function reporteExcel(Request $request){

        if($request->ajax()){

            // DE AQUI ESE EL ANTIGUO
            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;
            $empresa        = Auth::user()->empresa;

            $query = Factura::select(
                'facturas.numero_cafc',
                'facturas.estado',
                'facturas.codigo_descripcion',
                'facturas.tipo_factura',
                'facturas.uso_cafc',
                'facturas.nit',
                'facturas.cuf',
                'facturas.id',
                'facturas.fecha',
                'facturas.total as totalFactura',
                'facturas.razon_social',
                'facturas.numero_factura',
                'facturas.empresa_id',
                'facturas.siat_documento_sector_id',
                'facturas.usuario_creador_id',
                'facturas.descuento_adicional',

                'clientes.cedula',
                'clientes.nombres',
                'clientes.ap_paterno',
                'clientes.ap_materno',

                'detalles.servicio_id as servicio_id',
                'detalles.descripcion_adicional',
                'detalles.precio',
                'detalles.cantidad',
                'detalles.total',
                'detalles.descuento',
                'detalles.importe',

                'servicios.descripcion as descripcion_servicio',
                'servicios.precio as precio_servicio'

                )
            ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
            ->join('detalles', 'detalles.factura_id','=','facturas.id')
            ->join('servicios', 'servicios.id', '=','detalles.servicio_id')
            ->where('facturas.empresa_id', $empresa_id)
            ->where('facturas.sucursal_id', $sucursal_id)
            ->where('facturas.punto_venta_id', $punto_venta_id)
            ;

            if(!is_null($request->input('buscar_nro_factura'))){
                $numero_factura = $request->input('buscar_nro_factura');
                $query->where('facturas.numero_factura', $numero_factura);
            }

            if(!is_null($request->input('buscar_nro_cedula'))){
                $cedula = $request->input('buscar_nro_cedula');
                $query->where('clientes.cedula', $cedula);
            }

            if(!is_null($request->input('buscar_nit'))){
                $nit = $request->input('buscar_nit');
                $query->where('facturas.nit', $nit);
            }

            if(!is_null($request->input('buscar_fecha_inicio')) && !is_null($request->input('buscar_fecha_fin'))){
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"]);
            }

            $facturas = $query->get();
            // $facturas = $query->toSql();
            // dd(
            //     $facturas,
            //     $empresa_id,
            //     $sucursal_id,
            //     $punto_venta_id
            // );

            // generacion del excel
            $fileName = 'Facturas.xlsx';
            $libro = new Spreadsheet();
            $hoja = $libro->getActiveSheet();

            // Ajustar ancho de columnas
            $hoja->getColumnDimension('A')->setWidth(15); // N°
            $hoja->getColumnDimension('B')->setWidth(25); // CLIENTE
            $hoja->getColumnDimension('C')->setWidth(25); // RAZON
            $hoja->getColumnDimension('D')->setWidth(15); // NIT
            $hoja->getColumnDimension('E')->setWidth(20); // FECHA
            $hoja->getColumnDimension('F')->setWidth(15); // MONTO
            $hoja->getColumnDimension('G')->setWidth(20); // SECTOR
            $hoja->getColumnDimension('H')->setWidth(20); // MODALIDAD
            $hoja->getColumnDimension('I')->setWidth(20); // ESTADO
            $hoja->getColumnDimension('J')->setWidth(20);
            $hoja->getColumnDimension('K')->setWidth(20);
            $hoja->getColumnDimension('L')->setWidth(20);
            $hoja->getColumnDimension('M')->setWidth(20);
            $hoja->getColumnDimension('N')->setWidth(20);
            $hoja->getColumnDimension('O')->setWidth(20);
            $hoja->getColumnDimension('P')->setWidth(20);
            $hoja->getColumnDimension('Q')->setWidth(20);
            $hoja->getColumnDimension('R')->setWidth(20);
            $hoja->getColumnDimension('S')->setWidth(20);
            $hoja->getColumnDimension('T')->setWidth(20);

            // Añadir datos a la hoja de cálculo
            $hoja->setCellValue('A1', $empresa->nombre);
            $hoja->setCellValue('A2', "LISTADO DE FACTURAS");
            $hoja->setCellValue('A3', date('d/m/Y H:i:s'));

            $hoja->setCellValue('A4', "N° FAC");
            $hoja->setCellValue('B4', "CLIENTE");
            $hoja->setCellValue('C4', "RAZON");
            $hoja->setCellValue('D4', "NIT");
            $hoja->setCellValue('E4', "FECHA");
            $hoja->setCellValue('F4', "PRECIO SERVICIO");
            $hoja->setCellValue('G4', "SECTOR");
            $hoja->setCellValue('H4', "MODALIDAD");
            $hoja->setCellValue('I4', "ESTADO");
            $hoja->setCellValue('J4', "USUARIO");
            $hoja->setCellValue('K4', "SERVICIO_ID");
            $hoja->setCellValue('L4', "PRODUCTO / SERVICIO");
            $hoja->setCellValue('M4', "DESCRIPCION ADICIONAL");
            $hoja->setCellValue('N4', "PRECIO DEL SERVICIO VENTA");
            $hoja->setCellValue('O4', "CANTIDAD");
            $hoja->setCellValue('P4', "MONTO TOTAL");
            $hoja->setCellValue('Q4', "DESCUENTO");
            $hoja->setCellValue('R4', "DESCUENTO ADICIONAL");
            $hoja->setCellValue('S4', "IMPORTE PAGADO");
            $hoja->setCellValue('T4', "IMPORTE PAGADO TOTAL");

            $encabezadoStyle =[
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];

            $hoja->mergeCells('A1:T1');
            $hoja->mergeCells('A2:T2');
            $hoja->mergeCells('A3:T3');

            $hoja->getStyle('A1')->applyFromArray($encabezadoStyle);
            $hoja->getStyle('A2')->applyFromArray($encabezadoStyle);
            $hoja->getStyle('A3')->applyFromArray($encabezadoStyle);

            // Aplicar márgenes y formato a los encabezados
            $encabezadoStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFFFE0B2', // Color de fondo
                    ],
                ],
            ];
            $hoja->getStyle('A4:T4')->applyFromArray($encabezadoStyle);

            $contadorInicio              = 5;
            $numeroFacturaAnterior       = null;
            $numeroImportePagadoAnterior = null;
            foreach($facturas  as $key => $fac){
                $num_fac = $fac->uso_cafc == "Si" ? "N° Cafc:".$fac->numero_cafc : $fac->numero_factura;

                // // Comprobar si el número de factura actual es igual al anterior
                // if ($num_fac === $numeroFacturaAnterior) {
                //     // Si son iguales, solo configurar el contenido de la celda
                //     // (no necesitas hacer nada aquí si solo quieres combinar más adelante)
                //     // Puedes opcionalmente dejar la celda en blanco o mantener el valor
                // } else {
                //     // Si son diferentes, configurar la celda normalmente
                //     $hoja->setCellValue('A' . $contadorInicio, $num_fac);
                // }

                $hoja->setCellValue('A'.$contadorInicio, $num_fac);
                $hoja->setCellValue('B'.$contadorInicio, $fac->nombres." ".$fac->ap_paterno." ".$fac->ap_materno);
                $hoja->setCellValue('C'.$contadorInicio, $fac->razon_social);
                $hoja->setCellValue('D'.$contadorInicio, $fac->nit);
                $hoja->setCellValue('E'.$contadorInicio, $fac->fecha);
                $hoja->setCellValue('F'.$contadorInicio, $fac->precio_servicio);
                $sector = $fac->siat_tipo_documento_sector->codigo_clasificador == "8" ? "Fac. Tasa Cero" : "Fac. Com. Venta";
                $hoja->setCellValue('G'.$contadorInicio, $sector);
                $modalidad = $fac->tipo_factura == 'offline' ? "Fuera Linea": "Linea";
                $hoja->setCellValue('H'.$contadorInicio, $modalidad);
                $estado = !is_null($fac->estado) ? $fac->estado : "Vigente" ;
                $hoja->setCellValue('I'.$contadorInicio, $estado);
                $hoja->setCellValue('J'.$contadorInicio, $fac->usuarioCreador->nombres." ".$fac->usuarioCreador->ap_paterno." ".$fac->usuarioCreador->ap_materno);

                $hoja->setCellValue('K'.$contadorInicio, $fac->servicio_id);
                $hoja->setCellValue('L'.$contadorInicio, $fac->descripcion_servicio);
                $hoja->setCellValue('M'.$contadorInicio, $fac->descripcion_adicional);
                $hoja->setCellValue('N'.$contadorInicio, $fac->precio);
                $hoja->setCellValue('O'.$contadorInicio, $fac->cantidad);
                $hoja->setCellValue('P'.$contadorInicio, $fac->total);
                $hoja->setCellValue('Q'.$contadorInicio, $fac->descuento);
                $hoja->setCellValue('R'.$contadorInicio, $fac->descuento_adicional);
                $hoja->setCellValue('S'.$contadorInicio, $fac->importe);
                $hoja->setCellValue('T'.$contadorInicio, $fac->totalFactura);

                // Si es el primer registro o el número de factura ha cambiado, incrementar el contador
                // if ($num_fac !== $numeroFacturaAnterior) {
                if ($num_fac == $numeroFacturaAnterior) {
                    // Combinar celdas de la columna R (por ejemplo) si los números de factura son iguales
                    if ($contadorInicio > 1 && $numeroFacturaAnterior !== null) {
                        // Combina las celdas desde la fila anterior hasta la fila actual
                        $hoja->mergeCells('R' . ($contadorInicio - 1) . ':R' . $contadorInicio);
                        $hoja->mergeCells('T' . ($contadorInicio - 1) . ':T' . $contadorInicio);
                    }

                    // Actualiza el número de factura anterior
                }

                $numeroFacturaAnterior = $num_fac;
                $contadorInicio++;
            }

            // Aplicar bordes a las celdas de datos
            $hoja->getStyle('A5:T'.($contadorInicio-1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Establecer los encabezados para forzar la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'. $fileName .'"');
            header('Cache-Control: max-age=0');

            // Guardar el archivo
            $writer = new Xlsx($libro);
            $writer->save('php://output');
            exit;

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;

    }

    // ********************  PRUEBAS FACUTRAS SINCRONIZACION   *****************************
    public function pruebas(){



        // $empresa  = Empresa::find(1);
        // $sucursal = Sucursal::where('empresa_id', $empresa->id)
        //                             ->first();

        // $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
        //                         ->first();

        // $cuis = Cuis::where('punto_venta_id', $puntoVenta->id)
        //                     ->where('sucursal_id', $sucursal->id)
        //                     ->where('codigo_ambiente', $empresa->codigo_ambiente)
        //                     ->first();

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;

        $empresa     = Empresa::find($empresa_id);
        $puntoVenta = PuntoVenta::find($punto_venta_id);
        $sucursal    = Sucursal::find($sucursal_id);

        // SACAMOS EL CUIS VIGENTE
        $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);


        $siat = app(SiatController::class);

        // dd(
        //     "api_token => ".$empresa->api_token,
        //     "url_facturacionSincronizacion => ".$empresa->url_facturacionSincronizacion,
        //     "codigo_ambiente => ".$empresa->codigo_ambiente,
        //     "codigoPuntoVenta => ".$puntoVenta->codigoPuntoVenta,
        //     "codigo_sistema => ".$empresa->codigo_sistema,
        //     "codigo_sucursal => ".$sucursal->codigo_sucursal,
        //     "codigo => ".$cuis->codigo,
        //     "nit => ".$empresa->nit
        // );

        // for ($i = 1; $i <= 1 ; $i++) {
        for ($i = 1; $i <= 50 ; $i++) {

            $sincronizarActividades                         = json_decode($siat->sincronizarActividades(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarFechaHora                           = json_decode($siat->sincronizarFechaHora(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarListaActividadesDocumentoSector     = json_decode($siat->sincronizarListaActividadesDocumentoSector(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarListaLeyendasFactura                = json_decode($siat->sincronizarListaLeyendasFactura(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarListaMensajesServicios              = json_decode($siat->sincronizarListaMensajesServicios(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarListaProductosServicios             = json_decode($siat->sincronizarListaProductosServicios(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaEventosSignificativos    = json_decode($siat->sincronizarParametricaEventosSignificativos(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaMotivoAnulacion          = json_decode($siat->sincronizarParametricaMotivoAnulacion(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaPaisOrigen               = json_decode($siat->sincronizarParametricaPaisOrigen(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoDocumentoIdentidad(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoDocumentoSector      = json_decode($siat->sincronizarParametricaTipoDocumentoSector(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoEmision              = json_decode($siat->sincronizarParametricaTipoEmision(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoHabitacion           = json_decode($siat->sincronizarParametricaTipoHabitacion(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoMetodoPago           = json_decode($siat->sincronizarParametricaTipoMetodoPago(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoMoneda               = json_decode($siat->sincronizarParametricaTipoMoneda(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTipoPuntoVenta           = json_decode($siat->sincronizarParametricaTipoPuntoVenta(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaTiposFactura             = json_decode($siat->sincronizarParametricaTiposFactura(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            $sincronizarParametricaUnidadMedida             = json_decode($siat->sincronizarParametricaUnidadMedida(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));

            var_dump($sincronizarActividades);
            echo "<br><br><br>";
            var_dump($sincronizarFechaHora);
            echo "<br><br><br>";
            var_dump($sincronizarListaActividadesDocumentoSector);
            echo "<br><br><br>";
            var_dump($sincronizarListaLeyendasFactura);
            echo "<br><br><br>";
            var_dump($sincronizarListaMensajesServicios);
            echo "<br><br><br>";
            var_dump($sincronizarListaProductosServicios);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaEventosSignificativos);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaMotivoAnulacion);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaPaisOrigen);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoDocumentoIdentidad);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoDocumentoSector);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoEmision);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoHabitacion);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoMetodoPago);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoMoneda);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoPuntoVenta);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTiposFactura);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaUnidadMedida);
            echo "****************** => <h1>".$i."</h1><= ******************";
            sleep(3);
        }
    }
    // ********************  PRUEBAS FACUTRAS SINCRONIZACION   *****************************

    // ********************  CREACION MASIVA FACTURAACION   *****************************
    public function emiteFacturaMasa(Request $request){


        // Crear las variables con los valores correspondientes TASA CERO
        $nitEmisor                    = null;
        $razonSocialEmisor            = null;
        $municipio                    = null;
        $telefono                     = null;
        $numeroFactura                = null;
        $cuf                          = null;
        $cufd                         = null;
        $codigoSucursal               = null;
        $direccion                    = null;
        $codigoPuntoVenta             = null;
        $fechaEmision                 = "2024-07-10T19:45:27.882";
        $nombreRazonSocial            = "FLORES";
        $codigoTipoDocumentoIdentidad = "5";
        $numeroDocumento              = "8401524016";
        $complemento                  = null;
        $codigoCliente                = "8401524016";
        $codigoMetodoPago             = "1";
        $numeroTarjeta                = null;
        $montoTotal                   = "500";
        $montoTotalSujetoIva          = "0";
        $codigoMoneda                 = "1";
        $tipoCambio                   = "1";
        $montoTotalMoneda             = "500";
        $montoGiftCard                = null;
        $descuentoAdicional           = "0";
        $codigoExcepcion              = "0";
        $cafc                         = null;
        $leyenda                      = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
        $usuario                      = "ROCIO TORRICO MARTINES";
        $codigoDocumentoSector        = "8";

        $actividadEconomica = "474110";
        $codigoProductoSin  = "38581";
        $codigoProducto     = "7";
        $descripcionItem    = "CAJA CHE";
        $cantidad           = "1.00";
        $unidadMedida       = "58";
        $precioUnitario     = "500.00";
        $montoDescuento     = "0.00";
        $subTotal           = "500";
        // $numeroSerie        = null;
        // $numeroImei         = null;

        $clienteId = "1";
        $pagos     = ["915"];



        // Crear las variables con los valores correspondientes COMPRA Y VENTA
        // $nitEmisor                    = null;
        // $razonSocialEmisor            = null;
        // $municipio                    = null;
        // $telefono                     = null;
        // $numeroFactura                = null;
        // $cuf                          = null;
        // $cufd                         = null;
        // $codigoSucursal               = null;
        // $direccion                    = null;
        // $codigoPuntoVenta             = null;
        // $fechaEmision                 = "2024-07-21T12:50:28.336";
        // $nombreRazonSocial            = "FLORES";
        // $codigoTipoDocumentoIdentidad = "5";
        // $numeroDocumento              = "8401524016";
        // $complemento                  = null;
        // $codigoCliente                = "8401524016";
        // $codigoMetodoPago             = "1";
        // $numeroTarjeta                = null;
        // $montoTotal                   = "1500";
        // $montoTotalSujetoIva          = "1500";
        // $codigoMoneda                 = "1";
        // $tipoCambio                   = "1";
        // $montoTotalMoneda             = "1500";
        // $montoGiftCard                = null;
        // $descuentoAdicional           = "0";
        // $codigoExcepcion              = "0";
        // $cafc                         = null;
        // $leyenda                      = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
        // $usuario                      = "JHOSELIN RAMIREZ MAMANI";
        // $codigoDocumentoSector        = "1";

        // $actividadEconomica = "620100";
        // $codigoProductoSin  = "83141";
        // $codigoProducto     = "1";
        // $descripcionItem    = "DESARROLLO DE SISTEMA";
        // $cantidad           = "1.00";
        // $unidadMedida       = "58";
        // $precioUnitario     = "1500.00";
        // $montoDescuento     = "0.00";
        // $subTotal           = "1500";
        // $numeroSerie        = null;
        // $numeroImei         = null;

        // $clienteId = "2";
        // $pagos     = ["998"];



        // // // ********* SIN CAFC *********
        // $numero_cafc = null;
        // $uso_cafc = "No";

        // ********* CON CAFC *********
        $numero_cafc = 2;
        $uso_cafc = "Si";

        $modalidad = "offline";

        $usuario                 = Auth::user();
        $empresa_id              = $usuario->empresa_id;
        $punto_venta_id          = $usuario->punto_venta_id;
        $sucursal_id             = $usuario->sucursal_id;
        $empresa_objeto          = Empresa::find($empresa_id);
        $punto_venta_objeto      = PuntoVenta::find($punto_venta_id);
        $sucursal_objeto         = Sucursal::find($sucursal_id);
        $documentos_sector_model = $empresa_objeto->empresasDocumentosTipoSector($codigoDocumentoSector);

        // Crear el array final con la estructura proporcionada
        $array = [
            "datos" => [
                "factura" => [
                    0 => [
                        "cabecera" => [
                            "nitEmisor"                    => $empresa_objeto->nit,
                            "razonSocialEmisor"            => $empresa_objeto->razon_social,
                            "municipio"                    => $empresa_objeto->municipio,
                            "telefono"                     => $empresa_objeto->celular,
                            "numeroFactura"                => $numeroFactura,
                            "cuf"                          => $cuf,
                            "cufd"                         => $cufd,
                            "codigoSucursal"               => $sucursal_objeto->codigo_sucursal,
                            "direccion"                    => $direccion,
                            "codigoPuntoVenta"             => $codigoPuntoVenta,
                            "fechaEmision"                 => $fechaEmision,
                            "nombreRazonSocial"            => $nombreRazonSocial,
                            "codigoTipoDocumentoIdentidad" => $codigoTipoDocumentoIdentidad,
                            "numeroDocumento"              => $numeroDocumento,
                            "complemento"                  => $complemento,
                            "codigoCliente"                => $codigoCliente,
                            "codigoMetodoPago"             => $codigoMetodoPago,
                            "numeroTarjeta"                => $numeroTarjeta,
                            "montoTotal"                   => $montoTotal,
                            "montoTotalSujetoIva"          => $montoTotalSujetoIva,
                            "codigoMoneda"                 => $codigoMoneda,
                            "tipoCambio"                   => $tipoCambio,
                            "montoTotalMoneda"             => $montoTotalMoneda,
                            "montoGiftCard"                => $montoGiftCard,
                            "descuentoAdicional"           => $descuentoAdicional,
                            "codigoExcepcion"              => $codigoExcepcion,
                            "cafc"                         => $cafc,
                            "leyenda"                      => $leyenda,
                            "usuario"                      => $usuario->name,
                            "codigoDocumentoSector"        => $codigoDocumentoSector,
                        ]
                    ],
                    1 => [
                        "detalle" => [
                            "actividadEconomica"        => $actividadEconomica,
                            "codigoProductoSin"         => $codigoProductoSin,
                            "codigoProducto"            => $codigoProducto,
                            "descripcion"               => $descripcionItem,
                            "cantidad"                  => $cantidad,
                            "unidadMedida"              => $unidadMedida,
                            "precioUnitario"            => $precioUnitario,
                            "montoDescuento"            => $montoDescuento,
                            "subTotal"                  => $subTotal,
                            // "numeroSerie"               => $numeroSerie,
                            // "numeroImei"                => $numeroImei,
                        ]
                    ]
                ]
            ],
            "datosCliente" => [
                "cliente_id"  => $clienteId,
                "pagos"       => $pagos,
                "numero_cafc" => $numero_cafc,
                "uso_cafc"    => $uso_cafc
            ],
            "modalidad" => $modalidad,
        ];

        // Imprimir el array final
        // print_r($arrayFinal);

        for ($k=1; $k <= 5000 ; $k++) {
        // for ($k=1; $k <= 2500 ; $k++) {
        // for ($k=1; $k <= 500 ; $k++) {
        // for ($k=1; $k <= 1 ; $k++) {

            echo $k."<br>";

            // PARA LA HORA
            $microtime                                                = microtime(true);
            $seconds                                                  = floor($microtime);
            $milliseconds                                             = round(($microtime - $seconds) * 1000);
            $formattedDateTime                                        = date("Y-m-d\TH:i:s.") . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);
            $array['datos']['factura'][0]['cabecera']['fechaEmision'] = $formattedDateTime;

            $datosRecepcion = $array['datosCliente'];
            if($datosRecepcion['uso_cafc'] === "Si"){
                $numeroFacturaEmpresa = $datosRecepcion['numero_cafc'];
            }else{
                $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
                $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));
            }
            // PARA EL NUMERO
            $array['datos']['factura'][0]['cabecera']['numeroFactura'] = $numeroFacturaEmpresa;

            // PARA LA MENSUALIDAD
            // $array['datos']['factura'][1]['detalle']['descripcion']         = "$k MENSUALIDAD";
            // $array['datos']['factura'][0]['cabecera']['periodoFacturado']   = "$k MENSUALIDAD / 2023";

            // ******** DE AQUI YA VIENE PARA LA GENERACION DE LA FACTUR ********
            $datos           = $array['datos'];
            $datosCliente    = $array['datosCliente'];
            $valoresCabecera = $datos['factura'][0]['cabecera'];
            $puntoVenta      = $punto_venta_objeto->codigoPuntoVenta;
            $tipo_factura    = $array['modalidad'];

            $nitEmisor          = str_pad($valoresCabecera['nitEmisor'],13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad($sucursal_objeto->codigo_sucursal,4,"0",STR_PAD_LEFT);
            $modalidad          = $empresa_objeto->codigo_modalidad;
            $numeroFactura      = str_pad($numeroFacturaEmpresa,10,"0",STR_PAD_LEFT);

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                $datosRecepcion       = $array['datosCliente'];
                // dd($datosRecepcion);
                if($datosRecepcion['uso_cafc'] === "Si"){
                    $datos['factura'][0]['cabecera']['cafc']          = $empresa_objeto->cafc;
                    $datos['factura'][0]['cabecera']['numeroFactura'] = $numeroFacturaEmpresa;
                }
                $tipoEmision        = 2;
            }

            $tipoFactura        = ($codigoDocumentoSector == "8")? 2 : 1; // Factura sin Derecho a Crédito Fiscal
            $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            // dd($nitEmisor, $fechaEmision, $sucursal, $modalidad, $tipoEmision, $tipoFactura, $tipoFacturaSector, $numeroFactura, $puntoVenta);

            $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

            // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
            $cliente = Cliente::find($datosCliente['cliente_id']);
            $swFacturaEnvio = true;
            if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                // $data['estado'] = "error_email";
                // $data['msg']    = "La persona no tiene correo";
                // return $data;
                $swFacturaEnvio = false;
            }
            $cliente->nit              = $datos['factura'][0]['cabecera']['numeroDocumento'];
            $cliente->razon_social     = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
            $cliente->save();

            // CODIGO DE JOEL ESETE LO HIZMOMOS NOSOTROS
            $cadenaConM11 = $cadena.$this->calculaDigitoMod11($cadena, 1, 9, false);
            if($tipo_factura === "online"){
                // if(!session()->has('scufd')){
                //     $siat = app(SiatController::class);
                //     $siat->verificarConeccion();
                // }
                // $scufd                  = session('scufd');
                // $scodigoControl         = session('scodigoControl');
                // $sdireccion             = session('sdireccion');
                // $sfechaVigenciaCufd     = session('sfechaVigenciaCufd');
            }else{
                // $cufdController             = app(CufdController::class);
                // $datosCufdOffLine           = $cufdController->sacarCufdVigenteFueraLinea();
                // if($datosCufdOffLine['estado'] === "success"){
                //     $scufd                  = $datosCufdOffLine['scufd'];
                //     $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                //     $sdireccion             = $datosCufdOffLine['sdireccion'];
                //     $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                // }else{

                // }
                $eventoSignificadoControlller = app(EventoSignificativoController::class);

                $empresa_id      = $empresa_objeto->id;
                $sucursal_id     = $sucursal_objeto->id;
                $punto_venta_id  = $punto_venta_objeto->id;
                $codigo_ambiente = $empresa_objeto->codigo_ambiente;

                $datosCufdOffLine             = $eventoSignificadoControlller->sacarCufdVigenteFueraLinea(
                    $empresa_id,
                    $sucursal_id,
                    $punto_venta_id,
                    $codigo_ambiente
                );

                if($datosCufdOffLine['estado'] === "success"){
                    $scufd                  = $datosCufdOffLine['scufd'];
                    $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                    $sdireccion             = $datosCufdOffLine['sdireccion'];
                    $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                }else{

                    $data['estado'] = "error";
                    $data['msg']    = "ERROR AL RECUPERAR EL CUFD ANTIGUO";

                    return $data;
                }
            }

            $cufPro                                         = $this->generarBase16($cadenaConM11).$scodigoControl;

            // $datos['factura'][0]['cabecera']['nitEmisor']         = $empresa_objeto->nit;
            // $datos['factura'][0]['cabecera']['razonSocialEmisor'] = $empresa_objeto->razon_social;
            // $datos['factura'][0]['cabecera']['municipio']         = $empresa_objeto->municipio;
            // $datos['factura'][0]['cabecera']['telefono']          = $empresa_objeto->celular;
            // $datos['factura'][0]['cabecera']['numeroFactura']     = $numeroFacturaEmpresa;
            // $datos['factura'][0]['cabecera']['codigoSucursal']    = $sucursal_objeto->codigo_sucursal;

            $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
            $datos['factura'][0]['cabecera']['cufd']                = $scufd;
            $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
            $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

            $temporal = $datos['factura'];

            if($codigoDocumentoSector == "8"){
                if($empresa_objeto->codigo_modalidad == "1"){
                    $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                            <facturaElectronicaTasaCero xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaElectronicaTasaCero.xsd">
                            </facturaElectronicaTasaCero>';
                }else{
                    $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                            <facturaComputarizadaTasaCero xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaComputarizadaTasaCero.xsd">
                            </facturaComputarizadaTasaCero>';
                }

                // $xml_temporal = new SimpleXMLElement($dar);
                // $this->formato_xml($temporal, $xml_temporal);

                // $xml_temporal->asXML("assets/docs/facturaxmlTasaCero.xml");
            }else{
                if($empresa_objeto->codigo_modalidad == "1"){
                    $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                            <facturaElectronicaCompraVenta xsi:noNamespaceSchemaLocation="facturaElectronicaCompraVenta.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                            </facturaElectronicaCompraVenta>';
                }else{
                    $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                            <facturaComputarizadaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd">
                            </facturaComputarizadaCompraVenta>';
                }

            }


            $xml_temporal = new SimpleXMLElement($dar);
            $this->formato_xml($temporal, $xml_temporal);

            $xml_temporal->asXML("assets/docs/facturaxmlTasaCero.xml");


            //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\
            if($empresa_objeto->codigo_modalidad == "1"){
                // $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
                $firmador = new FirmadorBoliviaSingle($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                $xmlFirmado = $firmador->firmarRuta('assets/docs/facturaxmlTasaCero.xml');
                file_put_contents('assets/docs/facturaxmlTasaCero.xml', $xmlFirmado);
            }
            // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

            // COMPRIMIMOS EL ARCHIVO A ZIP
            $gzdato = gzencode(file_get_contents('assets/docs/facturaxmlTasaCero.xml',9));
            $fiape = fopen('assets/docs/facturaxmlTasaCero.xml.zip',"w");
            fwrite($fiape,$gzdato);
            fclose($fiape);

            //  hashArchivo EL ARCHIVO
            $archivoZip = $gzdato;
            $hashArchivo = hash("sha256", file_get_contents('assets/docs/facturaxmlTasaCero.xml'));

            // ESTO ES PARA LA FACTURA LA CREACION
            $facturaVerdad                          = new Factura();
            $facturaVerdad->usuario_creador_id      = Auth::user()->id;
            $facturaVerdad->cliente_id              = $cliente->id;
            $facturaVerdad->empresa_id              = $empresa_objeto->id;
            $facturaVerdad->sucursal_id             = $sucursal_objeto->id;
            $facturaVerdad->punto_venta_id          = $punto_venta_objeto->id;
            $facturaVerdad->cufd_id                 = $datosCufdOffLine['scufd_id'];
            $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
            $facturaVerdad->nit                     = $empresa_objeto->nit;
            $facturaVerdad->razon_social            = $empresa_objeto->razon_social;

            if($datosRecepcion['uso_cafc'] === "Si")
                $facturaVerdad->numero_cafc          = $numeroFacturaEmpresa;
            else
                $facturaVerdad->numero_factura       = $numeroFacturaEmpresa;

            $facturaVerdad->facturado                = "Si";
            $facturaVerdad->total                    = $datos['factura'][0]['cabecera']['montoTotal'];
            $facturaVerdad->monto_total_subjeto_iva  = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            $facturaVerdad->descuento_adicional      = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            $facturaVerdad->cuf                      = $datos['factura'][0]['cabecera']['cuf'];
            $facturaVerdad->productos_xml            = file_get_contents('assets/docs/facturaxmlTasaCero.xml');
            $facturaVerdad->siat_documento_sector_id = $documentos_sector_model->id;
            $facturaVerdad->codigo_descripcion       = NULL;
            $facturaVerdad->codigo_recepcion         = NULL;
            $facturaVerdad->codigo_transaccion       = NULL;
            $facturaVerdad->descripcion              = NULL;
            $facturaVerdad->uso_cafc                 = ($datosRecepcion['uso_cafc'] === "Si")? "Si" : "No";
            $facturaVerdad->tipo_factura             = "offline";
            $facturaVerdad->registro_compra          = 'No';

            $facturaVerdad->save();

             // AHORA AREMOS PARA LOS PAGOS
             Detalle::whereIn('id', $datosCliente['pagos'])
             ->update([
                 'estado'     => 'Finalizado',
                 'factura_id' => $facturaVerdad->id
             ]);

            if($tipo_factura === "online"){
                // $siat = app(SiatController::class);
                // $for = json_decode($siat->recepcionFactura($archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo));
                // if($for->estado === "error"){
                //     $codigo_descripcion = null;
                //     $codigo_trancaccion = null;
                //     $descripcion        = null;
                //     $codigo_recepcion   = null;
                // }else{
                //     if($for->resultado->RespuestaServicioFacturacion->transaccion){
                //         $codigo_recepcion   = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                //         $descripcion        = NULL;
                //     }else{
                //         $codigo_recepcion   = NULL;
                //         $descripcion        = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                //     }
                //     $codigo_descripcion     = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                //     $codigo_trancaccion     = $for->resultado->RespuestaServicioFacturacion->transaccion;
                // }
                // $data['estado'] = $codigo_descripcion;
            }else{
                $codigo_descripcion = null;
                $codigo_recepcion   = null;
                $codigo_trancaccion = null;
                $descripcion        = null;
                $data['estado']     = 'OFFLINE';
            }

            // $facturaNew                     = Factura::find($facturaVerdad->id);
            // // $facturaNew->codigo_descripcion = $codigo_descripcion;
            // // $facturaNew->codigo_recepcion   = $codigo_recepcion;
            // // $facturaNew->codigo_transaccion = $codigo_trancaccion;
            // // $facturaNew->descripcion        = $descripcion;
            // // $facturaNew->cuis               = session('scuis');
            // // $facturaNew->cufd               = $scufd;
            // $facturaNew->fechaVigencia      = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
            // $facturaNew->save();
            // foreach ($datosVehiculo['pagos'] as $key => $pago_id) {
            //     $pago = Pago::find($pago_id);
            //     // dd($datosVehiculo['pagos'], $pago_id, $pago);
            //     $pago->estado       = "Pagado";
            //     $pago->factura_id   = $facturaNew->id;
            //     $pago->save();
            // }
            // ******** DE AQUI YA VIENE PARA LA GENERACION DE LA FACTUR ********








            // ENVIAMOS EL CORREO DE LA FACTURA
            // $nombre = $persona->nombres." ".$persona->apellido_paterno." ".$persona->apellido_materno;
            // $this->enviaCorreo(
            //     $persona->email,
            //     $nombre,
            //     $factura->numero,
            //     $factura->fecha,
            //     $factura->id
            // );

            // PARA VALIDAR EL XML
            // $this->validar();

            // dd($array);

            // return $data;



            echo $formattedDateTime."<br>";
            sleep(2);
        }
    }
    // ********************  CREACION MASIVA FACTURAACION   *****************************

    public function pruebaCompraVenta(Request $request){

        $usuario        = Auth::user();
        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;

        $empresa_objeto     = Empresa::find($empresa_id);
        $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
        $sucursal_objeto    = Sucursal::find($sucursal_id);

        $cuis_objeto       = Cuis::where('punto_venta_id', $punto_venta_objeto->id)
                            ->where('sucursal_id', $sucursal_objeto->id)
                            ->where('codigo_ambiente', $empresa_objeto->codigo_ambiente)
                            ->first();


        // dd(
        //     $usuario,
        //     $empresa_id,
        //     $punto_venta_id,
        //     $sucursal_id,
        //     $empresa_objeto,
        //     $punto_venta_objeto,
        //     $sucursal_objeto,
        //     $cuis_objeto
        // );


        $siat = app(SiatController::class);

        $cufdVigente = json_decode(
            $siat->verificarConeccion(
                $empresa_objeto->id,
                $sucursal_objeto->id,
                $cuis_objeto->id,
                $punto_venta_objeto->id,
                $empresa_objeto->codigo_ambiente
            ));

        $url5             = $empresa_objeto->url_recepcion_compras;
        $header           = $empresa_objeto->api_token;
        $codigoAmbiente   = $empresa_objeto->codigo_ambiente;
        $codigoPuntoVenta = $punto_venta_objeto->codigoPuntoVenta;
        $codigoSistema    = $empresa_objeto->codigo_sistema;
        $codigoSucursal   = $sucursal_objeto->codigo_sucursal;
        $cufd             = $cufdVigente->codigo;
        $cuis             = $cuis_objeto->codigo;
        $nit              = $empresa_objeto->nit;
        $fecha            = date('Y-m-d\TH:i:s.v');









        // $datos = $request->all();
        // $checkboxes = collect($datos)->filter(function ($value, $key) {
        //     return Str::startsWith($key, 'check_');
        // })->toArray();

        // dd($checkboxes, $datos, Auth::user());

        // $evento_significativo_id = $request->input('evento_significativo_id');
        // $empresa_id              = Auth::user()->empresa_id;
        // $sucursal_id             = Auth::user()->sucursal_id;
        // $punto_venta_id          = Auth::user()->punto_venta_id;

        // $evento_significativo = EventoSignificativo::find($evento_significativo_id);
        // $empresa              = Empresa::find($empresa_id);
        // $sucursal             = Sucursal::find($sucursal_id);
        // $punto_venta          = PuntoVenta::find($punto_venta_id);
        // $cuis                 = $empresa->cuisVigente($sucursal_id,$punto_venta_id, $empresa->codigo_ambiente);

        // $codigo_evento_significativo    = $evento_significativo->codigoRecepcionEventoSignificativo;
        // $siat                           = app(SiatController::class);
        // $codigo_cafc_contingencia       = NULL;
        // $codigo_cafc_contingencia       = $empresa->cafc;

        $fechaActual                    = date('Y-m-d\TH:i:s.v');
        $fechaEmicion                   = $fechaActual;

        $cantidadFacturas = 0;

        // $rutaCarpeta = "assets/docs/paqueteCompras";
        // // Verificar si la carpeta existe
        // if (!file_exists($rutaCarpeta))
        //     mkdir($rutaCarpeta, 0755, true);

        // // Obtener lista de archivos en la carpeta
        // $archivos = glob($rutaCarpeta . '/*');
        // // Eliminar cada archivo
        // foreach ($archivos as $archivo) {
        //     if (is_file($archivo))
        //         unlink($archivo);
        // }
        // $file = public_path('assets/docs/paqueteCompras.tar.gz');
        // if (file_exists($file))
        //     unlink($file);

        // $file = public_path('assets/docs/paqueteCompras.tar');
        // if (file_exists($file))
        //     unlink($file);

        $idsToUpdate = [];



        // $ar = explode("_",$key);
        // $factura = Factura::find($ar[1]);

        // $idsToUpdate[] = (int)$ar[1];

        // $xml                            = $factura->productos_xml;
        $rita  = public_path('assets/docs/paqueteCompras/facturaCompraxml.xml');
        $xml                            = file_get_contents($rita);
        // $uso_cafc                       = $request->input("uso_cafc");
        $archivoXML                     = new SimpleXMLElement($xml);

        // GUARDAMOS EN LA CARPETA EL XML
        $archivoXML->asXML("assets/docs/paquete/paqueteComprasNew.xml");
        $cantidadFacturas++;



        // foreach($checkboxes as $key => $chek){
        //     $ar = explode("_",$key);
        //     $factura = Factura::find($ar[1]);

        //     $idsToUpdate[] = (int)$ar[1];

        //     $xml                            = $factura->productos_xml;
        //     // $uso_cafc                       = $request->input("uso_cafc");
        //     $archivoXML                     = new SimpleXMLElement($xml);

        //     // GUARDAMOS EN LA CARPETA EL XML
        //     $archivoXML->asXML("assets/docs/paquete/facturaxmlCompras$ar[1].xml");
        //     $contado++;
        // }

        // Ruta de la carpeta que deseas comprimir
        // $rutaCarpeta = "assets/docs/paqueteCompras"; //funciona!!
        $rutaCarpeta = "assets/docs/paquete";

        // Nombre y ruta del archivo TAR resultante
        // $archivoTar = "assets/docs/paqueteCompras.tar"; //funciona!!!
        $archivoTar = "assets/docs/paquete.tar";

        // Crear el archivo TAR utilizando la biblioteca PharData
        $tar = new PharData($archivoTar);
        $tar->buildFromDirectory($rutaCarpeta);

        // Ruta y nombre del archivo comprimido en formato Gzip
        // $archivoGzip = "assets/docs/paqueteCompras.tar.gz";//funciona!!!
        $archivoGzip = "assets/docs/paquete.tar.gz";

        // Comprimir el archivo TAR en formato Gzip
        // $comandoGzip = "gzip -c $archivoTar > $archivoGzip";
        // exec($comandoGzip);

        // ESTE ES OTRO CHEEE
        // Abre el archivo .gz en modo de escritura
        $gz = gzopen($archivoGzip, 'wb');
        // Abre el archivo .tar en modo de lectura
        $archivo = fopen($archivoTar, 'rb');
        // Lee el contenido del archivo .tar y escribe en el archivo .gz
        while (!feof($archivo)) {
            gzwrite($gz, fread($archivo, 8192));
        }
        // Cierra los archivos
        fclose($archivo);
        gzclose($gz);

        // Leer el contenido del archivo comprimido
        $contenidoArchivo = file_get_contents($archivoGzip);

        // Calcular el HASH (SHA256) del contenido del archivo
        $hashArchivo = hash('sha256', $contenidoArchivo);

        $gestion = 2024;
        $periodo = 8;

        // dd(
        //     $url5,
        //     $header,
        //     $codigoAmbiente,
        //     $codigoPuntoVenta,
        //     $codigoSistema,
        //     $codigoSucursal,
        //     $cufd,
        //     $cuis,
        //     $nit,

        //     $contenidoArchivo,
        //     $cantidadFacturas,
        //     $fechaEmicion,
        //     $gestion,
        //     $hashArchivo,
        //     $periodo
        // );

        $consultaCompras = json_decode(
            $siat->recepcionPaqueteCompras(
                $url5,
                $header,
                $codigoAmbiente,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $cufd,
                $cuis,
                $nit,

                $contenidoArchivo,
                $cantidadFacturas,
                $fechaEmicion,
                $gestion,
                $hashArchivo,
                $periodo
            ));

        dd($consultaCompras);

    }


    // ===================  FUNCIOENES PROTEGIDAS  ========================
    protected function calculaDigitoMod11($cadena, $numDig, $limMult, $x10){

        $mult = 0;
        $suma = 0;
        $dig = 0;
        $i = 0;
        $n = 0;

        if (!$x10) {
            $numDig = 1;
        }

        for ($n = 1; $n <= $numDig; $n++) {
            $suma = 0;
            $mult = 2;

            for ($i = strlen($cadena) - 1; $i >= 0; $i--) {
                $suma += ($mult * intval(substr($cadena, $i, 1)));

                if (++$mult > $limMult) {
                    $mult = 2;
                }
            }

            if ($x10) {
                $dig = (($suma * 10) % 11) % 10;
            } else {
                $dig = $suma % 11;
            }

            if ($dig == 10) {
                $cadena .= "1";
            }

            if ($dig == 11) {
                $cadena .= "0";
            }

            if ($dig < 10) {
                $cadena .= strval($dig);
            }
        }

        return substr($cadena, strlen($cadena) - $numDig, $numDig);
    }

    protected function generarBase16($caracteres) {
        $pString = ltrim($caracteres, '0');
        $vValor  = gmp_init($pString);
        return strtoupper(gmp_strval($vValor, 16));
    }

    protected function formato_xml($temporal, $xml_temporal){
        $ns_xsi = "http://www.w3.org/2001/XMLSchema-instance";
        foreach($temporal as $key => $value){
            if(is_array($value)){
                if(!is_numeric($key)){
                    $subnodo = $xml_temporal->addChild("$key");
                    $this->formato_xml($value, $subnodo);
                }else{
                    $this->formato_xml($value, $xml_temporal);
                }
            }else{
                if($value == null && $value <> '0'){
                    $hijo = $xml_temporal->addChild("$key",htmlspecialchars("$value"));
                    $hijo->addAttribute('xsi:nil','true', $ns_xsi);
                }else{
                    $xml_temporal->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }
    }

    protected function enviaCorreo($correo, $nombre, $numero, $fecha, $factura_id){

        $usuario = Auth::user();
        $empresa = Auth::user()->empresa;

        // ********************************  ESTE SI FUNCIONA AHROA *******************

        $to         = $correo;
        $subject    = 'FACTURA EN LINEA '.$empresa->nombre;

        // Cargar el contenido de la vista del correo
        $templatePath = resource_path('views/mail/correoFactura.blade.php');
        $templateContent = file_get_contents($templatePath);
        $fecha = date('d/m/Y H:m:s');
        $data = [
            'title'        => 'Bienvenido a mi aplicación',
            'content'      => 'Gracias por unirte a nosotros. Esperamos que disfrutes de tu tiempo aquí.',
            'name'         => $nombre,
            'number'       => $numero,
            'date'         => $fecha,
            'empresa_name' => $empresa->nombre,
            'logo'         => asset("assets/img")."/".$empresa->logo
        ];

        foreach ($data as $key => $value)
            $templateContent = str_replace('{{ $' . $key . ' }}', $value, $templateContent);

        $mail = new PHPMailer(true);

        // Configuración de los parámetros SMTP
        // $smtpHost       = 'mail.micarautolavado.com';
        // $smtpPort       =  465;
        // $smtpUsername   = 'admin@micarautolavado.com';
        // $smtpPassword   = '-Z{DjF[D@y8G';

        $smtpHost       = 'mail.facbol.com';
        $smtpPort       =  465;
        $smtpUsername   = 'facturacion@facbol.com';
        $smtpPassword   = 'j}RXM[5&#yzz';

        try {
            $mail->setFrom($smtpUsername, $empresa->nombre);
            $mail->addAddress($to);

            // Agregar direcciones de correo electrónico en copia (CC)
            // $mail->addCC('admin@comercio-latino.com', 'Administracion Comercio Latino');
            // $mail->addCC('soporte@comercio-latino.com', 'Soporte Comercio Latino');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $templateContent;

            $factura       = Factura::find($factura_id);
            $xml           = $factura['productos_xml'];
            $archivoXML    = new SimpleXMLElement($xml);
            $cabeza        = (array) $archivoXML;
            $cuf           = (string)$cabeza['cabecera']->cuf;
            $numeroFactura = (string)$cabeza['cabecera']->numeroFactura;
            // Genera el texto para el código QR
            $textoQR = $empresa->url_verifica."?nit=".$empresa->nit."&cuf=".$factura->cuf."&numero=".$numeroFactura."&t=1";

            // Genera la ruta temporal para guardar la imagen del código QR
            $rutaImagenQR = storage_path('app/public/qr_code.png');

            // Genera el código QR y guarda la imagen en la ruta temporal
            QrCode::generate($textoQR, $rutaImagenQR);
            $pdf = PDF::loadView('factura.pdf.generaPdfFacturaNewCv', compact('factura', 'archivoXML','rutaImagenQR', 'empresa'))->setPaper('letter');

            // Genera la ruta donde se guardará el archivo PDF
            $rutaPDF = storage_path("app/public/factura_$factura->cuf.pdf");
            $rutaXML = storage_path("app/public/facturaxml_$factura->cuf.xml");

            // Guarda el PDF en la ruta especificada
            $pdf->save($rutaPDF);
            $archivoXML->asXML($rutaXML);

            $mail->addAttachment($rutaPDF, "Factura.pdf"); // Adjuntar archivo PDF
            $mail->addAttachment($rutaXML, "Factura.xml"); // Adjuntar archivo XML

            $mail->send();

            // Verifica si el archivo PDF existe antes de intentar eliminarlo
            if (file_exists($rutaPDF))
                unlink($rutaPDF);

            // Verifica si el archivo XML existe antes de intentar eliminarlo
            if (file_exists($rutaXML))
                unlink($rutaXML);

            // return 'Correo enviado correctamente';
            $data['estado'] = 'success';
            $data['msg']    = 'Correo enviado correctamente';

        } catch (Exception $e) {
            $data['estado'] = 'error';
            $data['msg'] = 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
        }

        return $data;
    }

    protected function enviaCorreoAnulacion($correo, $nombre, $numero, $fecha){

        $usuario = Auth::user();
        $empresa = $usuario->empresa;

        $to         = $correo;
        $subject    = 'ANULACION DE FACTURA EN LINEA '.$empresa->nombre;

            // Cargar el contenido de la vista del correo
            $templatePath = resource_path('views/mail/correoAnulacionFactura.blade.php');
            $templateContent = file_get_contents($templatePath);
            $fecha = date('d/m/Y H:m:s');
            $data = [
                'title'   => 'Bienvenido a mi aplicación',
                'content' => 'Gracias por unirte a nosotros. Esperamos que disfrutes de tu tiempo aquí.',
                'name'    => $nombre,
                'number'  => $numero,
                'date'    => $fecha,
                'empresa' => $empresa->nombre,
                'logo'    => asset("assets/img")."/".$empresa->logo
            ];

            foreach ($data as $key => $value)
                $templateContent = str_replace('{{ $' . $key . ' }}', $value, $templateContent);

            // Configuración de los parámetros SMTP
            $smtpHost       = 'mail.facbol.com';
            $smtpPort       =  465;
            $smtpUsername   = 'facturacion@facbol.com';
            $smtpPassword   = 'j}RXM[5&#yzz';

            // $smtpUsername   = 'sistemas@comercio-latino.com';
            // $smtpPassword   = 'j@xKuZ(65VNK';

            $mail = new PHPMailer(true);

            try {
                // $mail->isSMTP();
                // $mail->Host         = $smtpHost;
                // $mail->Port         = $smtpPort;
                // $mail->SMTPAuth     = true;
                // $mail->Username     = $smtpUsername;
                // $mail->Password     = $smtpPassword;
                // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS; no va este
                // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_SMTPS;
                // ... Configura los parámetros SMTP ...
                $mail->setFrom($smtpUsername, $empresa->nombre);
                $mail->addAddress($to);

                // Agregar direcciones de correo electrónico en copia (CC)
                // $mail->addCC('admin@comercio-latino.com', 'Administracion Comercio Latino');
                // $mail->addCC('soporte@comercio-latino.com', 'Soporte Comercio Latino');

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $templateContent;

                //$factura       = Factura::find($factura_id);
                //$xml           = $factura['productos_xml'];
                //$archivoXML    = new SimpleXMLElement($xml);
                //$cabeza        = (array) $archivoXML;
                //$cuf           = (string)$cabeza['cabecera']->cuf;
                //$numeroFactura = (string)$cabeza['cabecera']->numeroFactura;
                  // Genera el texto para el código QR
                //$textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
                  // Genera la ruta temporal para guardar la imagen del código QR
                //$rutaImagenQR = storage_path('app/public/qr_code.png');
                  // Genera el código QR y guarda la imagen en la ruta temporal
                // QrCode::generate($textoQR, $rutaImagenQR);
                // $pdf = PDF::loadView('pdf.generaPdfFacturaNew', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');

                // // Genera la ruta donde se guardará el archivo PDF
                // $rutaPDF = storage_path('app/public/factura.pdf');
                // // Guarda el PDF en la ruta especificada
                // $pdf->save($rutaPDF);
                // $pdfPath = "assets/docs/facturapdf.pdf";
                // $xmlPath = "assets/docs/facturaxml.xml";

                // $mail->addAttachment($rutaPDF, 'Factura.pdf'); // Adjuntar archivo PDF
                // $mail->addAttachment($xmlPath, 'Factura.xml'); // Adjuntar archivo XML


                $mail->send();

                // return 'Correo enviado correctamente';
                $data['estado'] = 'success';
                $data['msg']    = 'Correo enviado correctamente';

            } catch (Exception $e) {
                $data['estado'] = 'error';
                $data['msg'] = 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
                // return 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
            }

        // $mail = new CorreoAnulacion($nombre, $numero, $fecha);
        // $response = Mail::to($correo)->send($mail);
    }
    // ===================  FUNCIOENES PROTEGIDAS  ========================

}
