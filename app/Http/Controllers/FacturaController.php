<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Detalle;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\Servicio;
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

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        //
    }
}
