<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Suscripcion;
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

            $empresa = Empresa::find($empresa_id);

            $suscripciones = Suscripcion::where('empresa_id', $empresa_id)
                                        ->get();

            $data['text']    = 'Se proceso con exito';
            $data['estado']  = 'success';
            $data['listado'] = view('empresa.ajaxListadoSuscripcion')
                                ->with(compact('suscripciones'))
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

            // dd($request->all());

            $suscripcion                     = new Suscripcion();
            $suscripcion->usuario_creador_id = Auth::user()->id;
            $suscripcion->empresa_id         = $request->input('empresa_id_new_plan');
            $suscripcion->plan_id            = $request->input('plan_id_new_plan');
            $suscripcion->fecha_inicio       = $request->input('fecha_inicio_new_plan');
            $suscripcion->fecha_fin          = $request->input('fecha_fin_new_plan');
            $suscripcion->descripcion        = $request->input('descripcion_new_plan');
            $suscripcion->save();


            $data['text']    = 'Se proceso con exito';
            $data['estado']  = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Suscripcion $suscripcion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suscripcion $suscripcion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Suscripcion $suscripcion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suscripcion $suscripcion)
    {
        //
    }
}
