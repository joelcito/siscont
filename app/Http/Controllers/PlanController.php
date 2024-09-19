<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function listado(Request $request)
    {
        return view('plan.listado');
    }

    public function agregarPlan(Request $request)
    {
        if($request->ajax()){

            $plan_id= $request->input('plan_id');

            if($plan_id == "0"){
                $plan                       = new Plan();
                $plan->usuario_creador_id   = Auth::user()->id;
            }else{
                $plan = Plan::find($plan_id);
                $plan->usuario_modificador_id = Auth::user()->id;
            }

            $plan->precio               = $request->input('precio');
            $plan->nombre               = $request->input('nombre');
            $plan->tipo_plan            = $request->input('tipo_plan');
            $plan->cantidad_factura     = $request->input('cantidad_factura');
            $plan->cantidad_sucursal    = $request->input('cantidad_sucursal');
            $plan->cantidad_punto_venta = $request->input('cantidad_punto_venta');
            $plan->cantidad_usuario     = $request->input('cantidad_usuario');
            $plan->cantidad_producto    = $request->input('cantidad_producto');
            $plan->cantidad_clientes    = $request->input('cantidad_clientes');

            $plan->save();

            $data['text']    = 'Se proceso con exito';
            $data['estado']  = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function ajaxListado(Request $request)
    {
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
        $planes = Plan::all();
        return view('plan.ajaxListado')->with(compact('planes'))->render();
    }
}
