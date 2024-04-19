<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Detalle;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\Servicio;
use App\Models\SiatTipoDocumentoIdentidad;
use App\Models\SiatTipoMetodoPagos;
use App\Models\SiatTipoMoneda;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $empresa     = Empresa::find($empresa_id);
            $punto_venta = PuntoVenta::find($punto_venta_id);
            $sucursal    = Sucursal::find($sucursal_id);

            $datosCliente = $request->input('datosCliente');
            // $empresa_id   = $datosCliente['empresa'];

            // dd(
            //     $datosCliente,
            //     $empresa_id
            // );


            $datos           = $request->input('datos');
            $valoresCabecera = $datos['factura'][0]['cabecera'];
            $puntoVenta      = $punto_venta->codigoPuntoVenta;
            $tipo_factura    = $request->input('modalidad');
            $swFacturaEnvio  = true;


            $nitEmisorEmpresa     = $empresa->nit;
            $sucursalEmpresa      = $sucursal->codigo_sucursal;
            $numeroFacturaEmpresa = $this->numeroFactura($empresa->id, $sucursal->id);

            // dd(
            //     $nitEmisorEmpresa,
            //     $sucursalEmpresa,
            //     $numeroFacturaEmpresa
            // );


            $nitEmisor          = str_pad($nitEmisorEmpresa,13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad($sucursalEmpresa,4,"0",STR_PAD_LEFT);
            $modalidad          = 1;
            $numeroFactura      = str_pad(($numeroFacturaEmpresa==null? 1 : $numeroFacturaEmpresa ),10,"0",STR_PAD_LEFT);

            // dd(
            //     $datos,
            //     $datosCliente,
            //     $valoresCabecera,
            //     $puntoVenta,
            //     $tipo_factura,
            //     $swFacturaEnvio,

            //     $nitEmisor,
            //     $fechaEmision,
            //     $sucursal,
            //     $modalidad,
            //     $numeroFactura
            // );

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                $datosRecepcion       = $request->input('datosRecepcion');
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
                if(!session()->has('scufd')){
                    $siat = app(SiatController::class);
                    $siat->verificarConeccion();
                }
                $scufd                  = session('scufd');
                $scodigoControl         = session('scodigoControl');
                $sdireccion             = session('sdireccion');
                $sfechaVigenciaCufd     = session('sfechaVigenciaCufd');
            }else{
                $cufdController             = app(CufdController::class);
                $datosCufdOffLine           = $cufdController->sacarCufdVigenteFueraLinea();
                if($datosCufdOffLine['estado'] === "success"){
                    $scufd                  = $datosCufdOffLine['scufd'];
                    $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                    $sdireccion             = $datosCufdOffLine['sdireccion'];
                    $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                }else{

                }
            }

            $cufPro                                                 = $this->generarBase16($cadenaConM11).$scodigoControl;

            // dd($scufd);

            $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
            $datos['factura'][0]['cabecera']['cufd']                = $scufd;
            $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
            $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

            $temporal = $datos['factura'];
            $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <facturaElectronicaCompraVenta xsi:noNamespaceSchemaLocation="facturaElectronicaCompraVenta.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        </facturaElectronicaCompraVenta>';
            $xml_temporal = new SimpleXMLElement($dar);
            $this->formato_xml($temporal, $xml_temporal);

            $xml_temporal->asXML("assets/docs/facturaxml.xml");


            //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\

            $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
            $xmlFirmado = $firmador->firmarRuta('assets/docs/facturaxml.xml');
            file_put_contents('assets/docs/facturaxml.xml', $xmlFirmado);

            // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

            // COMPRIMIMOS EL ARCHIVO A ZIP
            $gzdato = gzencode(file_get_contents('assets/docs/facturaxml.xml',9));
            $fiape = fopen('assets/docs/facturaxml.xml.zip',"w");
            fwrite($fiape,$gzdato);
            fclose($fiape);

            //  hashArchivo EL ARCHIVO
            $archivoZip = $gzdato;
            $hashArchivo = hash("sha256", file_get_contents('assets/docs/facturaxml.xml'));


        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function numeroFactura($empresa_id, $sucursal_id){
        $numeroFactura = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->max('numero_factura');

        return $numeroFactura;
    }

}
