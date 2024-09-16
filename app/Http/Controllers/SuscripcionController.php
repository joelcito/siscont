<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\Plan;
use App\Models\PuntoVenta;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SuscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function ajaxListadoSuscripcion(Request $request)
    {
        if($request->ajax()){

            $empresa_id = $request->input('empresa');
            $empresa    = Empresa::find($empresa_id);

            $suscripciones = Suscripcion::where('empresa_id', $empresa_id)
                                        ->orderBy('id', 'desc')
                                        ->get();

            $isAdmin = Auth::user()->isAdmin();

            $data['text']    = 'Se proceso con exito';
            $data['estado']  = 'success';
            $data['listado'] = view('empresa.ajaxListadoSuscripcion')
                                ->with(compact(
                                    'suscripciones',
                                    'isAdmin'
                                    ))
                                ->render();



        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function guardarSuscripcion(Request $request)
    {
        if($request->all()){

            $usaurio    = Auth::user();
            $empresa_id = $request->input('empresa_id_new_plan');
            $suscripcion_id = $request->input('suscripcion_id_new_plan');

            if($suscripcion_id == "0"){
                $suscripcion                               = new Suscripcion();
                $suscripcion->usuario_creador_id           = $usaurio->id;

                $suscripcion->fecha_inicio                 = $request->input('fecha_inicio_new_plan')." ".date('H:i:s');
                $suscripcion->fecha_fin                    = $request->input('fecha_fin_new_plan')." ".date('H:i:s');
                $this->cambiaEstadoSuscripcionVigente($empresa_id);

            }else{
                $suscripcion                         = Suscripcion::find($suscripcion_id);
                $suscripcion->usuario_modificador_id = $usaurio->id;

                $hora = explode(' ', $suscripcion->fecha_fin);

                $suscripcion->fecha_inicio                 = $request->input('fecha_inicio_new_plan')." ".$hora[1];
                $suscripcion->fecha_fin                    = $request->input('fecha_fin_new_plan')." ".$hora[1];

            }

            $suscripcion->empresa_id                   = $empresa_id;
            $suscripcion->plan_id                      = $request->input('plan_id_new_plan');
            $suscripcion->descripcion                  = $request->input('descripcion_new_plan');
            $suscripcion->ampliacion_cantidad_facturas = $request->input('ampliacion_cantidad_facturas_new_plan');
            $suscripcion->save();

            $data['text']    = 'Se proceso con exito';
            $data['estado']  = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function obtenerSuscripcionVigenteEmpresa(Empresa $empresa)
    {
        $empresa_id = $empresa->id;

        $fecha_actual = date('Y-m-d H:i:s');

        $suscripcion = Suscripcion::where('empresa_id', $empresa_id)
                                ->where('fecha_inicio', '<=', $fecha_actual)
                                ->where('fecha_fin', '>=', $fecha_actual)
                                ->whereNull('estado')
                                ->first();

        return $suscripcion;

    }

    public function verificarRegistroServicioProductoByPlan(Plan $plan, Empresa $empresa)
    {
        $cantidadServicioProducto = Servicio::where('empresa_id', $empresa->id)
                                                    ->count();

        return $cantidadServicioProducto < $plan->cantidad_producto ? true : false;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function verificarRegistroClienteByPlan(Plan $plan, Empresa $empresa)
    {
        $cantidadCliente = Cliente::where('empresa_id', $empresa->id)->count();

        return $cantidadCliente < $plan->cantidad_clientes ? true : false;
    }

    /**
     * Update the specified resource in storage.
     */
    public function verificarRegistroFacturaByPlan(Plan $plan, Empresa $empresa, Suscripcion $suscripcion)
    {

        $cantidadFactura = Factura::where('empresa_id', $empresa->id)->count();

        return $cantidadFactura < ($plan->cantidad_factura + $suscripcion->ampliacion_cantidad_facturas) ? true : false;
    }

    public function verificarRegistroPuntoVentaByPlan(Plan $plan, Sucursal $sucursal)
    {

        $cantidadPuntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)->count();

        return $cantidadPuntoVenta < $plan->cantidad_punto_venta ? true : false;
    }

    public function verificarRegistroSucursalByPlan(Plan $plan, Empresa $empresa)
    {

        $cantidadSucursal = Sucursal::where('empresa_id', $empresa->id)->count();

        return $cantidadSucursal < $plan->cantidad_sucursal ? true : false;
    }

    public function verificarRegistroUsuarioByPlan(Plan $plan, Empresa $empresa)
    {

        $cantidadUsuario = User::where('empresa_id', $empresa->id)->count();

        return $cantidadUsuario < $plan->cantidad_usuario ? true : false;
    }

    private function cambiaEstadoSuscripcionVigente($empresa_id){

        // $b = false;

        $suscripcion = Suscripcion::where('empresa_id', $empresa_id)
                                    ->whereNull('estado')
                                    ->orderBy('id', 'DESC')
                                    ->first();

        if($suscripcion){
            $suscripcion->estado = 'Vencido';
            $suscripcion->save();
            //  $b = true;
        }

        // $data['estado'] = $b;
        // $data['objeto'] = $suscripcion;

        // return $data;
    }
}
