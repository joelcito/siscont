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

        return view('factura.formularioFacturacion')->with(compact('verificacionSiat', 'cuis', 'cufd', 'servicios'));
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
}
