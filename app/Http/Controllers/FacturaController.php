<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuis;
use App\Models\Detalle;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\Servicio;
use App\Models\SiatMotivoAnulacion;
use App\Models\SiatTipoDocumentoIdentidad;
use App\Models\SiatTipoMetodoPagos;
use App\Models\SiatTipoMoneda;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function formularioFacturacion(Request $request){

        $usuario = Auth::user();

        $empresa_id     = $usuario->empresa_id;
        $punto_venta_id = $usuario->punto_venta_id;
        $sucursal_id    = $usuario->sucursal_id;

        $empresa     = Empresa::find($empresa_id);
        $punto_venta = PuntoVenta::find($punto_venta_id);
        $sucursal    = Sucursal::find($sucursal_id);

        $url1   = $empresa->url_facturacionCodigos;
        $header = $empresa->api_token;

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
                            ->get();

        return view('factura.formularioFacturacion')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
    }

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

            $servicio            = json_decode($request->input('serivicio_id_venta'));
            $precio_venta        = $request->input('precio_venta');
            $cantidad_venta      = $request->input('cantidad_venta');
            $total_venta         = $request->input('total_venta');
            $cliente_id_escogido = $request->input('cliente_id_escogido');

            // dd(Auth::user());

            $detalle                     = new Detalle();
            $detalle->usuario_creador_id = Auth::user()->id;
            $detalle->empresa_id         = Auth::user()->empresa_id;
            $detalle->sucursal_id        = Auth::user()->sucursal_id;
            $detalle->punto_venta_id     = Auth::user()->punto_venta_id;
            $detalle->cliente_id         = $cliente_id_escogido;
            $detalle->servicio_id        = $servicio->id;
            $detalle->precio             = $precio_venta;
            $detalle->cantidad           = $cantidad_venta;
            $detalle->total              = $total_venta;
            $detalle->descuento          = 0;
            $detalle->importe            = ($cantidad_venta*$precio_venta);
            $detalle->fecha              = date('Y-m-d');
            $detalle->estado             = "Parapagar";
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

            $detalles = Detalle::where('cliente_id', $cliente_id)
                                ->where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->where('estado','Parapagar')
                                ->get();

            // TIP DE DOCUMENTO
            $tipoDocumento = SiatTipoDocumentoIdentidad::all();
            
            // TIP METO DE PAGO
            $tipoMetodoPago = SiatTipoMetodoPagos::all();

            // TIPO MONEDA
            $tipoMonedas = SiatTipoMoneda::all();

            $data['listado'] = view('factura.ajaxListadoDetalles')->with(compact('detalles', 'tipoDocumento', 'tipoMetodoPago', 'tipoMonedas'))->render();
            $data['estado'] = 'success';

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

            $datelles = Detalle::select('detalles.*', 'siat_unidad_medidas.codigo_clasificador', 'siat_producto_servicios.codigo_producto', 'siat_depende_actividades.codigo_caeb', 'servicios.descripcion')
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

    public function  emitirFactura(Request $request){
        if($request->ajax()){

            // dd($request->all());

            // ********************************* ESTO ES PARA GENERAR LA FACTURA *********************************            
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

            $datosCliente = $request->input('datosCliente');
            // $empresa_id   = $datosCliente['empresa'];

            // dd(
            //     $datosCliente,
            //     $empresa_id
            // );


            $datos           = $request->input('datos');
            $valoresCabecera = $datos['factura'][0]['cabecera'];
            $puntoVenta      = $punto_venta_objeto->codigoPuntoVenta;
            $tipo_factura    = $request->input('modalidad');
            $swFacturaEnvio  = true;

            $nitEmisorEmpresa     = $empresa_objeto->nit;
            $sucursalEmpresa      = $sucursal_objeto->codigo_sucursal;
            $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
            $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));

            $nitEmisor          = str_pad($nitEmisorEmpresa,13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad($sucursalEmpresa,4,"0",STR_PAD_LEFT);
            // $modalidad          = 1;
            $modalidad          = $empresa_objeto->codigo_modalidad;
            $numeroFactura      = str_pad($numeroFacturaEmpresa,10,"0",STR_PAD_LEFT);

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                // $datosRecepcion       = $request->input('datosRecepcion');
                $datosRecepcion       = $datosCliente;
                if($datosRecepcion['uso_cafc'] === "Si"){
                    $datos['factura'][0]['cabecera']['cafc'] = $datosRecepcion['codigo_cafc_contingencia'];
                }
                $tipoEmision        = 2;
            }

            $tipoFactura        = 1;
            $tipoFacturaSector  = str_pad(1,2,"0",STR_PAD_LEFT);;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

            // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
            $cliente = Cliente::find($datosCliente['cliente_id']);
            // $cliente = Cliente::find($vehiculo->cliente->id);
            if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                // $data['estado'] = "error_email";
                // $data['msg']    = "La persona no tiene correo";
                // return $data;
                $swFacturaEnvio = false;
            }
            $cliente->nit              = $request->input('datos')['factura'][0]['cabecera']['numeroDocumento'];
            $cliente->razon_social     = $request->input('datos')['factura'][0]['cabecera']['nombreRazonSocial'];
            $cliente->save();

            // CODIGO DE JOEL ESETE LO HIZMOMOS NOSOTROS
            $cadenaConM11 = $cadena.$this->calculaDigitoMod11($cadena, 1, 9, false);
            if($tipo_factura === "online"){
                // if(!session()->has('scufd')){
                //     $siat = app(SiatController::class);
                //     $siat->verificarConeccion();
                // }

                $siat = app(SiatController::class);

                // dd($siat->verificarConeccion(
                //     $empresa_objeto->id,
                //     $sucursal_objeto->id,
                //     $cuis_objeto->id,
                //     $punto_venta_objeto->id,
                //     $empresa_objeto->codigo_ambiente
                // ));

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

                // $scufd                  = session('scufd');
                // $scodigoControl         = session('scodigoControl');
                // $sdireccion             = session('sdireccion');
                // $sfechaVigenciaCufd     = session('sfechaVigenciaCufd');

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
                    $data['msg']    = "ERROR AL RECUPERAR EL CUFD ANTIGUO";

                    return $data;
                }
            }

            $cufPro                                                 = $this->generarBase16($cadenaConM11).$scodigoControl;

            // dd($scufd);
            $datos['factura'][0]['cabecera']['nitEmisor']         = $empresa_objeto->nit;
            $datos['factura'][0]['cabecera']['razonSocialEmisor'] = $empresa_objeto->razon_social;
            $datos['factura'][0]['cabecera']['municipio']         = $empresa_objeto->municipio;
            $datos['factura'][0]['cabecera']['telefono']          = $empresa_objeto->celular;
            $datos['factura'][0]['cabecera']['numeroFactura']     = $numeroFacturaEmpresa;
            $datos['factura'][0]['cabecera']['codigoSucursal']    = $sucursal_objeto->codigo_sucursal;
            


            $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
            $datos['factura'][0]['cabecera']['cufd']                = $scufd;
            $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
            $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

            $temporal = $datos['factura'];

            // dd($empresa_objeto->codigo_modalidad);

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

            $xml_temporal->asXML("assets/docs/facturaxml.xml");


            //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\
            if($empresa_objeto->codigo_modalidad == "1"){
                // $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
                // $xmlFirmado = $firmador->firmarRuta('assets/docs/facturaxml.xml');
                // file_put_contents('assets/docs/facturaxml.xml', $xmlFirmado);
            }
            // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

            // COMPRIMIMOS EL ARCHIVO A ZIP
            $gzdato = gzencode(file_get_contents('assets/docs/facturaxml.xml',9));
            $fiape = fopen('assets/docs/facturaxml.xml.zip',"w");
            fwrite($fiape,$gzdato);
            fclose($fiape);

            //  hashArchivo EL ARCHIVO
            $archivoZip = $gzdato;
            $hashArchivo = hash("sha256", file_get_contents('assets/docs/facturaxml.xml'));




            if($tipo_factura === "online"){

                $header                = $empresa_objeto->api_token;
                $url3                  = $empresa_objeto->url_servicio_facturacion_compra_venta;
                $codigoAmbiente        = $empresa_objeto->codigo_ambiente;
                $codigoDocumentoSector = $empresa_objeto->codigo_documento_sector;
                $codigoModalidad       = $empresa_objeto->codigo_modalidad;
                $codigoPuntoVenta      = $punto_venta_objeto->codigoPuntoVenta;
                $codigoSistema         = $empresa_objeto->codigo_sistema;
                $codigoSucursal        = $sucursal_objeto->codigo_sucursal;
                $scufd                 = $cufdVigente->codigo;
                $scuis                 = $cuis_objeto->codigo;
                $nit                   = $empresa_objeto->nit;

                // dd(
                //     $header,
                //     $url3,
                //     $codigoAmbiente,
                //     $codigoDocumentoSector,
                //     $codigoModalidad,
                //     $codigoPuntoVenta,
                //     $codigoSistema,
                //     $codigoSucursal,
                //     $scufd,
                //     $scuis,
                //     $nit
                // );

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

                    $archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo)
                );

                // NUEVO CODIGO PARA EVITAR ERROES DE GENERACION DE FACTURAS Y EVITAR QUE SE CREE MAS FACTURAS ASI NOMAS
                if($for->estado === "success"){

                    // dd($for);

                    // $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                    if($for->resultado->RespuestaServicioFacturacion->transaccion){
                        $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;

                        // ESTO ES PARA LA FACTURA LA CREACION
                        $facturaVerdad                          = new Factura();
                        $facturaVerdad->usuario_creador_id      = Auth::user()->id;
                        $facturaVerdad->cliente_id              = $cliente->id;
                        $facturaVerdad->empresa_id              = $empresa_objeto->id;
                        $facturaVerdad->sucursal_id             = $sucursal_objeto->id;
                        $facturaVerdad->punto_venta_id          = $punto_venta_objeto->id;
                        $facturaVerdad->cufd_id                 = $cufdVigente->id;
                        $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                        $facturaVerdad->nit                     = $empresa_objeto->nit;
                        $facturaVerdad->razon_social            = $empresa_objeto->razon_social;
                        $facturaVerdad->numero_factura          = $numeroFacturaEmpresa;
                        $facturaVerdad->facturado               = "Si";
                        $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                        $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                        $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        $facturaVerdad->codigo_transaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                        $facturaVerdad->descripcion             = NULL;
                        $facturaVerdad->uso_cafc                = "No";
                        $facturaVerdad->tipo_factura            = "online";

                        $facturaVerdad->save();





                        // $facturaVerdad->creador_id              = Auth::user()->id;
                        // $facturaVerdad->vehiculo_id             = $datosVehiculo['vehiculo_id'];
                        // $facturaVerdad->cliente_id              = $vehiculo->cliente_id;
                        // $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                        // $facturaVerdad->carnet                  = $vehiculo->cliente->cedula;
                        // $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];;
                        // $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                        // $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                        // $facturaVerdad->facturado               = "Si";
                        // $facturaVerdad->tipo_pago               = $request->input('tipo_pago');
                        // $facturaVerdad->monto_pagado            = $request->input('monto_pagado');
                        // $facturaVerdad->cambio_devuelto         = $request->input('cambio');
                        // // $facturaVerdad->estado_pago             = (((int)$facturaVerdad->monto_pagado - (int)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                        // $facturaVerdad->estado_pago             = (((double)$facturaVerdad->monto_pagado - (double)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                        // $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        // $facturaVerdad->codigo_metodo_pago_siat = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
                        // $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        // $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        // $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                        // $facturaVerdad->numero                  = $datos['factura'][0]['cabecera']['numeroFactura'];
                        // $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                        // $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        // $facturaVerdad->codigo_trancaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                        // $facturaVerdad->descripcion             = NULL;
                        // $facturaVerdad->cuis                    = session('scuis');
                        // $facturaVerdad->cufd                    = $scufd;
                        // $facturaVerdad->fechaVigencia           = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
                        // $facturaVerdad->tipo_factura            = $tipo_factura;
                        // $facturaVerdad->save();

                        // AHORA AREMOS PARA LOS PAGOS
                        Detalle::whereIn('id', $datosCliente['pagos'])
                                ->update([
                                    'estado'     => 'Finalizado',
                                    'factura_id' => $facturaVerdad->id
                                ]);

                        // if($datosVehiculo['realizo_pago'] === "true"){
                        //     $pago                = new Pago();
                        //     $pago->creador_id    = Auth::user()->id;
                        //     $pago->factura_id    = $facturaVerdad->id;
                        //     $pago->caja_id       = $datosVehiculo['caja'];
                        //     // $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
                        //     $pago->monto         = (double)$request->input('monto_pagado')-(double)$request->input('cambio');
                        //     $pago->descripcion   = "VENTA";
                        //     $pago->apertura_caja = "No";
                        //     $pago->fecha         = date('Y-m-d H:i:s');
                        //     $pago->tipo_pago     = $request->input('tipo_pago');
                        //     $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
                        //     $pago->save();
                        // }else{

                        // }

                        $data['estado'] = $codigo_descripcion;

                        // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                        // if($swFacturaEnvio){
                        //     $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                        //     $this->enviaCorreo(
                        //         $cliente->correo,
                        //         $nombre,
                        //         $facturaVerdad->numero,
                        //         $facturaVerdad->fecha,
                        //         $facturaVerdad->id
                        //     );
                        // }

                    }else{
                        $data['estado'] = "RECHAZADA";
                        // dd($for);
                        // $data['msg'] = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                        $data['msg'] = json_encode($for->resultado->RespuestaServicioFacturacion->mensajesList);
                    }

                    // dd($for, $data);

                }else{
                    $data['estado'] = "RECHAZADA";
                    $data['msg'] = $for->msg;
                }
                // dd($for);
                // if($for->estado === "error"){
                //     $codigo_descripcion = null;
                //     $codigo_trancaccion = null;
                //     $descripcion        = null;
                //     $codigo_recepcion   = null;
                // }else{
                //     if($for->resultado->RespuestaServicioFacturacion->transaccion){
                //         $codigo_recepcion = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                //         $descripcion      = NULL;
                //     }else{
                //         $codigo_recepcion = NULL;
                //         $descripcion      = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                //     }
                //     $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                //     $codigo_trancaccion = $for->resultado->RespuestaServicioFacturacion->transaccion;
                // }
                // $data['estado'] = $codigo_descripcion;
            }else{


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
                 $facturaVerdad->numero_factura          = $numeroFacturaEmpresa;
                 $facturaVerdad->facturado               = "Si";
                 $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                 $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                 $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                 $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                 $facturaVerdad->codigo_descripcion      = NULL;
                 $facturaVerdad->codigo_recepcion        = NULL;
                 $facturaVerdad->codigo_transaccion      = NULL;
                 $facturaVerdad->descripcion             = NULL;
                 $facturaVerdad->uso_cafc                = "No";
                 $facturaVerdad->tipo_factura            = "offline";

                 $facturaVerdad->save();


                // // ESTO ES PARA LA FACTURA LA CREACION
                // $facturaVerdad                          = new Factura();
                // $facturaVerdad->creador_id              = Auth::user()->id;
                // $facturaVerdad->vehiculo_id             = $datosVehiculo['vehiculo_id'];
                // $facturaVerdad->cliente_id              = $vehiculo->cliente_id;
                // $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                // $facturaVerdad->carnet                  = $vehiculo->cliente->cedula;
                // $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];;
                // $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                // $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                // $facturaVerdad->facturado               = "Si";
                // $facturaVerdad->tipo_pago               = $request->input('tipo_pago');
                // $facturaVerdad->monto_pagado            = $request->input('monto_pagado');
                // $facturaVerdad->cambio_devuelto         = $request->input('cambio');
                // $facturaVerdad->estado_pago             = (((double)$facturaVerdad->monto_pagado - (double)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                // // $facturaVerdad->estado_pago             = (((int)$facturaVerdad->monto_pagado - (int)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                // $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                // $facturaVerdad->codigo_metodo_pago_siat = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
                // $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                // $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                // $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                // // $facturaVerdad->numero                  = $datos['factura'][0]['cabecera']['numeroFactura'];
                // $facturaVerdad->codigo_descripcion      = NULL;
                // $facturaVerdad->codigo_recepcion        = NULL;
                // $facturaVerdad->codigo_trancaccion      = NULL;
                // $facturaVerdad->descripcion             = NULL;

                // if($datosRecepcion['uso_cafc'] === "Si"){
                //     $facturaVerdad->numero_cafc = $datos['factura'][0]['cabecera']['numeroFactura'];
                //     $facturaVerdad->uso_cafc    = "si";
                // }else{
                //     $facturaVerdad->numero = $datos['factura'][0]['cabecera']['numeroFactura'];
                // }

                // $facturaVerdad->cuis                    = session('scuis');
                // $facturaVerdad->cufd                    = $scufd;
                // $facturaVerdad->fechaVigencia           = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
                // $facturaVerdad->tipo_factura            = $tipo_factura;
                // $facturaVerdad->save();

                // // AHORA AREMOS PARA LOS PAGOS
                // Detalle::whereIn('id', $datosVehiculo['pagos'])
                //         ->update(['estado' => 'Finalizado']);

                // if($datosVehiculo['realizo_pago'] === "true"){
                //     $pago                = new Pago();
                //     $pago->creador_id    = Auth::user()->id;
                //     $pago->factura_id    = $facturaVerdad->id;
                //     $pago->caja_id       = $datosVehiculo['caja'];
                //     // $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
                //     $pago->monto         = (double)$request->input('monto_pagado')-(double)$request->input('cambio');
                //     $pago->descripcion   = "VENTA";
                //     $pago->apertura_caja = "No";
                //     $pago->fecha         = date('Y-m-d H:i:s');
                //     $pago->tipo_pago     = $request->input('tipo_pago');
                //     $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
                //     $pago->save();
                // }else{

                // }

                // if($swFacturaEnvio){
                //     // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                //     $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                //     $this->enviaCorreo(
                //         $cliente->correo,
                //         $nombre,
                //         $facturaVerdad->numero,
                //         $facturaVerdad->fecha,
                //         $facturaVerdad->id
                //     );
                // }

                $data['estado']     = 'OFFLINE';
            }


        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function numeroFactura($empresa_id, $sucursal_id, $punto_venta_id){
        $numeroFactura = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->max('numero_factura');

        return $numeroFactura;
    }

    public function listado(Request $request){

        $siat_motivo_anulaciones = SiatMotivoAnulacion::all();

        return view('factura.listado')->with(compact('siat_motivo_anulaciones'));
    }

    public function ajaxListadoFacturas(Request $request){
        if($request->ajax()){

            // dd(Auth::user());

            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;

            $facturas = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->get();

            $data['listado'] = view('factura.ajaxListadoFacturas')->with(compact('facturas'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function anularFactura(Request $request){
        if($request->ajax()){

            // dd($request->all());
            $factura_id       = $request->input('factura_id');
            $motivo_anulacion = $request->input('codigoMotivoAnulacion');

            $factura     = Factura::find($factura_id);
            $empresa_id  = $factura->empresa_id;
            $empresa     = Empresa::find($empresa_id);
            $sucursal    = Sucursal::find($factura->sucursal_id);
            $punto_venta = PuntoVenta::find($factura->punto_venta_id);
            $cuis        = $empresa->cuisVigente($sucursal->id, $punto_venta->id, $empresa->codigo_ambiente);


            $siat       = app(SiatController::class);

            $cufdVigente = json_decode(
                $siat->verificarConeccion(
                    $empresa->id,
                    $sucursal->id,
                    $cuis->id,
                    $punto_venta->id,
                    $empresa->codigo_ambiente
                ));


            // dd($cufdVigente);

            $header                = $empresa->api_token;
            $url3                  = $empresa->url_servicio_facturacion_compra_venta;
            $codigoAmbiente        = $empresa->codigo_ambiente;
            $codigoDocumentoSector = $empresa->codigo_documento_sector;
            $codigoModalidad       = $empresa->codigo_modalidad;
            $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
            $codigoSistema         = $empresa->codigo_sistema;
            $codigoSucursal        = $sucursal->codigo_sucursal;
            $scufd                 = $cufdVigente->codigo;
            $scuis                 = $cuis->codigo;
            $nit                   = $empresa->nit;
            
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

                $motivo_anulacion, $factura->cuf
            ));

            // dd($respuesta);

            if($respuesta->estado == "success"){
                if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                    $factura->estado = 'Anulado';
    
                    // PARA ELIMINAR LOS PAGOS
                    // Pago::where('factura_id', $fatura->id)->delete();
    
                    // PARA ELIMINAR LOS DETALLES
                    Detalle::where('factura_id', $factura->id)->delete();
    
                    // $cliente = Cliente::find($factura->cliente_id);
    
                    // $correo = $cliente->correo;
                    // $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                    // $numero = $factura->numero;
                    // $fecha  = $factura->fecha;
    
                    //protected function enviaCorreoAnulacion($correo, $nombre, $numero, $fecha){
    
                    // $this->enviaCorreoAnulacion($correo, $nombre, $numero, $fecha );
    
                    $data['estado'] = "success";
                }else{
                    $factura->descripcion = $respuesta->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                    $data['estado']       = "error";
                    $data['descripcion']  = $respuesta->resultado->RespuestaServicioFacturacion;
                    // dd($respuesta->resultado->RespuestaServicioFacturacion);
                }
                $factura->save();
            }else{
                $data['text']   = 'No existe';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
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

}
