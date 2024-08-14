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
use App\Models\SiatMotivoAnulacion;
use App\Models\SiatTipoDocumentoIdentidad;
use App\Models\SiatTipoMetodoPagos;
use App\Models\SiatTipoMoneda;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PharData;
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

        // dd(
        //     "sucursal_id ".$sucursal_id,
        //     "punto_venta_id ".$punto_venta_id,
        //     "codigo_ambiente ".$empresa->codigo_ambiente
        // );

        // SACAMOS EL CUIS VIGENTE
        $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

        // dd($cuis, $sucursal_id, $punto_venta_id, $empresa->codigo_ambiente, $usuario, $sucursal_id);


        // SACAMOS EL CUFD VIGENTE
        $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

        $servicios = Servicio::where('empresa_id', $empresa_id)
                            ->get();

        return view('factura.formularioFacturacion')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
    }

    public function formularioFacturacionTasaCero(Request $request){

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

        // dd($verificacionSiat);

        // SACAMOS EL CUIS VIGENTE
        $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

        // SACAMOS EL CUFD VIGENTE
        $cufd = $siat->verificarConeccion($empresa_id, $sucursal_id, $cuis->id, $punto_venta->id, $empresa->codigo_ambiente);

        $servicios = Servicio::where('empresa_id', $empresa_id)
                            ->get();

        return view('factura.formularioFacturacionTasaCero')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
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

    public function  emitirFactura(Request $request){
        // dd($request->all());
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

            $datosRecepcion = $datosCliente;
            if($datosRecepcion['uso_cafc'] === "Si"){
                $numeroFacturaEmpresa = $datosRecepcion['numero_cafc'];
            }else{
                $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
                $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));
            }

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
                if($datosRecepcion['uso_cafc'] === "Si"){
                    $datos['factura'][0]['cabecera']['cafc']          = $empresa_objeto->cafc;
                    $datos['factura'][0]['cabecera']['numeroFactura'] = $datosRecepcion['numero_cafc'];
                }
                $tipoEmision = 2;
            }

            $tipoFactura        = ($empresa_objeto->codigo_documento_sector == 8)? 2 : 1; // Factura sin Derecho a Crédito Fiscal
            $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
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

            $nombreArchivo = $cufPro."_".$numeroFacturaEmpresa."_".$nitEmisorEmpresa;

            $xml_temporal->asXML("assets/docs/facturaxml_$nombreArchivo.xml");


            //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\
            if($empresa_objeto->codigo_modalidad == "1"){
                // $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
                // dd($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                $firmador = new FirmadorBoliviaSingle($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                $xmlFirmado = $firmador->firmarRuta("assets/docs/facturaxml_$nombreArchivo.xml");
                file_put_contents("assets/docs/facturaxml_$nombreArchivo.xml", $xmlFirmado);
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




            if($tipo_factura === "online"){

                $header                 = $empresa_objeto->api_token;
                $url3                   = $empresa_objeto->url_servicio_facturacion_compra_venta;
                $codigoAmbiente         = $empresa_objeto->codigo_ambiente;
                $codigoDocumentoSector  = $empresa_objeto->codigo_documento_sector;
                $codigoModalidad        = $empresa_objeto->codigo_modalidad;
                $codigoPuntoVenta       = $punto_venta_objeto->codigoPuntoVenta;
                $codigoSistema          = $empresa_objeto->codigo_sistema;
                $codigoSucursal         = $sucursal_objeto->codigo_sucursal;
                $scufd                  = $cufdVigente->codigo;
                $scuis                  = $cuis_objeto->codigo;
                $nit                    = $empresa_objeto->nit;
                $tipoFacturaDocumento   = 1;

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
                    $tipoFacturaDocumento,

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
                        $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                        $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->productos_xml           = file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml");
                        $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                        $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        $facturaVerdad->codigo_transaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                        $facturaVerdad->descripcion             = NULL;
                        $facturaVerdad->uso_cafc                = "No";
                        $facturaVerdad->registro_compra         = 'No';
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
                    // dd($for);
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

                if($datosRecepcion['uso_cafc'] === "Si")
                    $facturaVerdad->numero_cafc          = $numeroFacturaEmpresa;
                else
                    $facturaVerdad->numero_factura       = $numeroFacturaEmpresa;

                $facturaVerdad->facturado               = "Si";
                $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                $facturaVerdad->productos_xml           = file_get_contents("assets/docs/facturaxml_$nombreArchivo.xml");
                $facturaVerdad->codigo_descripcion      = NULL;
                $facturaVerdad->codigo_recepcion        = NULL;
                $facturaVerdad->codigo_transaccion      = NULL;
                $facturaVerdad->descripcion             = NULL;
                $facturaVerdad->uso_cafc                = ($datosRecepcion['uso_cafc'] === "Si")? "Si" : "No";
                $facturaVerdad->tipo_factura            = "offline";
                $facturaVerdad->registro_compra         = 'No';


                 $facturaVerdad->save();

                 // AHORA AREMOS PARA LOS PAGOS
                Detalle::whereIn('id', $datosCliente['pagos'])
                 ->update([
                     'estado'     => 'Finalizado',
                     'factura_id' => $facturaVerdad->id
                 ]);

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

    public function  emitirFacturaTasaCero(Request $request){
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

            $datosRecepcion = $datosCliente;
            if($datosRecepcion['uso_cafc'] === "Si"){
                $numeroFacturaEmpresa = $datosRecepcion['numero_cafc'];
            }else{
                $numeroFacturaEmpresa = $this->numeroFactura($empresa_objeto->id, $sucursal_objeto->id, $punto_venta_objeto->id);
                $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));
            }

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

                if($datosRecepcion['uso_cafc'] === "Si"){
                    $datos['factura'][0]['cabecera']['cafc']          = $empresa_objeto->cafc;
                    $datos['factura'][0]['cabecera']['numeroFactura'] = $datosRecepcion['numero_cafc'];
                }
                $tipoEmision = 2;
            }

            $tipoFactura        = ($empresa_objeto->codigo_documento_sector == 8)? 2 : 1; // Factura sin Derecho a Crédito Fiscal
            $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            // dd($nitEmisor, $fechaEmision.$sucursal, $modalidad, $tipoEmision, $tipoFactura, $tipoFacturaSector, $numeroFactura ,$puntoVenta);

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
                // $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
                $firmador = new FirmadorBoliviaSingle($empresa_objeto->archivop12, $empresa_objeto->contrasenia);
                $xmlFirmado = $firmador->firmarRuta("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml");
                file_put_contents("assets/docs/facturaxmlTasaCero_$nombreArchivo.xml", $xmlFirmado);
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




            if($tipo_factura === "online"){

                $header                 = $empresa_objeto->api_token;
                $url3                   = $empresa_objeto->url_servicio_facturacion_compra_venta;
                $codigoAmbiente         = $empresa_objeto->codigo_ambiente;
                $codigoDocumentoSector  = $empresa_objeto->codigo_documento_sector;
                $codigoModalidad        = $empresa_objeto->codigo_modalidad;
                $codigoPuntoVenta       = $punto_venta_objeto->codigoPuntoVenta;
                $codigoSistema          = $empresa_objeto->codigo_sistema;
                $codigoSucursal         = $sucursal_objeto->codigo_sucursal;
                $scufd                  = $cufdVigente->codigo;
                $scuis                  = $cuis_objeto->codigo;
                $nit                    = $empresa_objeto->nit;
                $tipoFacturaDocumento   = 2;

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
                    $tipoFacturaDocumento,

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
                        $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                        $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxmlTasaCero.xml');
                        $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                        $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        $facturaVerdad->codigo_transaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                        $facturaVerdad->descripcion             = NULL;
                        $facturaVerdad->uso_cafc                = "No";
                        $facturaVerdad->tipo_factura            = "online";
                        $facturaVerdad->registro_compra         = 'No';

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

            if($datosRecepcion['uso_cafc'] === "Si")
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
                $facturaVerdad->uso_cafc                = ($datosRecepcion['uso_cafc'] === "Si")? "Si" : "No";
                $facturaVerdad->tipo_factura            = "offline";
                $facturaVerdad->registro_compra         = 'No';


                $facturaVerdad->save();

                 // AHORA AREMOS PARA LOS PAGOS
                Detalle::whereIn('id', $datosCliente['pagos'])
                 ->update([
                     'estado'     => 'Finalizado',
                     'factura_id' => $facturaVerdad->id
                 ]);


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

            // dd(Auth::user());

            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;

            $facturas = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)

                                // ->whereNull('estado')
                                // ->where('estado','ANULADO')

                                ->orderBy('id', 'desc')
                                ->limit(100)
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
            $tipoFacturaDocumento  = ($empresa->codigo_documento_sector == 8)? 2 : 1;

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
                    $data['msg']          = $respuesta->resultado;
                    // dd($respuesta->resultado->RespuestaServicioFacturacion);
                }
                $factura->save();
            }else{
                $data['text']   = 'No existe';
                $data['estado'] = 'error';
                $data['msg']    = $respuesta;
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

            $empresa     = Empresa::find($empresa_id);
            $sucursal    = Sucursal::find($sucursal_id);
            $punto_venta = PuntoVenta::find($punto_venta_id);
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
            $cuf1                  = $factura->cuf;
            $tipoFacturaDocumento  = ($empresa->codigo_documento_sector == 8)? 2 : 1;

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

            $siat = app(SiatController::class);

            $verificarNit = json_decode($siat->verificarNit(
                $empresa_objeto->url_facturacionCodigos,
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

        return view('factura.formularioFacturacionCv')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios', 'empresa'));
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

        for ($i = 1; $i <= 1 ; $i++) {

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

        $clienteId = "7";
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



        // ********* SIN CAFC *********
        $numero_cafc = null;
        $uso_cafc = "No";

        // ********* CON CAFC *********
        // $numero_cafc = 1;
        // $uso_cafc = "Si";

        $modalidad = "offline";

        $usuario            = Auth::user();
        $empresa_id         = $usuario->empresa_id;
        $punto_venta_id     = $usuario->punto_venta_id;
        $sucursal_id        = $usuario->sucursal_id;
        $empresa_objeto     = Empresa::find($empresa_id);
        $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
        $sucursal_objeto    = Sucursal::find($sucursal_id);

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


        for ($k=1; $k <= 500 ; $k++) {
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

            $tipoFactura        = ($empresa_objeto->codigo_documento_sector == 8)? 2 : 1; // Factura sin Derecho a Crédito Fiscal
            $tipoFacturaSector  = str_pad($valoresCabecera['codigoDocumentoSector'],2,"0",STR_PAD_LEFT);;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            // dd($nitEmisor, $fechaEmision, $sucursal, $modalidad, $tipoEmision, $tipoFactura, $tipoFacturaSector, $numeroFactura, $puntoVenta);

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

            if($empresa_objeto->codigo_documento_sector == 8){
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

            $facturaVerdad->facturado               = "Si";
            $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
            $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
            $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxmlTasaCero.xml');
            $facturaVerdad->codigo_descripcion      = NULL;
            $facturaVerdad->codigo_recepcion        = NULL;
            $facturaVerdad->codigo_transaccion      = NULL;
            $facturaVerdad->descripcion             = NULL;
            $facturaVerdad->uso_cafc                = ($datosRecepcion['uso_cafc'] === "Si")? "Si" : "No";
            $facturaVerdad->tipo_factura            = "offline";
            $facturaVerdad->registro_compra         = 'No';

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

    public function armaJson(Request $request) {
        $ciudades = storage_path('app/public/paises.xlsx'); // Ruta local de tu archivo Excel

        // Verificar si el archivo existe
        if (!file_exists($ciudades)) {
            return 'El archivo no existe.';
        }

        // Leer el archivo Excel y obtener los datos como una matriz
        $data = Excel::toArray([], $ciudades);

        // Procesar los datos del archivo Excel
        foreach ($data[0] as $key => $row) {
            // dd($row);
            if($row[1] != 'country_code' && $row[3] != 'State_Code'){
                // Manejar cada fila del archivo Excel
                // Por ejemplo, imprimir el contenido de cada columna
                // echo 'Columna 1: ' . $row[1] . ', Columna 2: ' . $row[2] . ', Columna 3: ' . $row[3] . '<br>';

                $ciudadesA[$key][0] = $row[1];
                $ciudadesA[$key][1] = $row[2];
                $ciudadesA[$key][2] = $row[3];

            }
        }

        echo "######################################";
        // Procesar los datos de la segunda hoja del archivo Excel
        if (isset($data[1])) {
            foreach ($data[1] as $key => $row) {
                if ($row[0] != 'Pais' && $row[1] != 'Codigo') {
                    // Manejar cada fila del archivo Excel de la segunda hoja
                    // echo 'Segunda hoja - Columna 1: ' . $row[0] . ', Columna 2: ' . $row[1] . '<br>';
                    $paises[$key][0] = $row[0];
                    $paises[$key][1] = $row[1];
                }
            }
        } else {
            echo "La segunda hoja no existe en el archivo Excel.";
        }


        echo "------------------------------- RECORREMOS -------------------------------";

        // dd($paises, $ciudadesA);
        $jsonCiudades = [];
        // Recorrer el array $paises
        foreach ($paises as $key => $pais) {

            $filtroCodigo = $paises[$key][1];

            // Usando array_filter() con una función callback
            $paisesFiltrados = array_filter($ciudadesA, function($ciudadesA1) use ($filtroCodigo) {
                return $ciudadesA1[0] === $filtroCodigo;
            });

            // dd($paisesFiltrados);

            // for($ciudadesA as $i => $ciudad){

            // }

            $paies = [
                // 'pais'   => $paises[$key+1][0],
                // 'codigo' => $paises[$key+1][1]

                'pais'   => $paises[$key][0],
                'codigo' => $paises[$key][1],
                'estado' => [$paisesFiltrados]
            ];



            $jsonCiudades[] = $paies;
            // dd($pais);
            echo 'Pais ' . ($key + 1) . ': ' . $pais[0] . ', Codigo: ' . $pais[1] . '<br>';
        }

        // dd($jsonCiudades);
        dd(json_encode($jsonCiudades));



        return 'Archivo importado exitosamente desde local.';
    }
    // ===================  FUNCIOENES PROTEGIDAS  ========================

}
