<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listado(Request $request){
        return view('rol.listado');
    }

    /**
     * Show the form for creating a new resource.
     */
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
        $roles = Rol::all();
        return view('rol.ajaxListado')->with(compact('roles'))->render();
    }

    public function agregarRol(Request $request){
        if($request->ajax()){

            $rol                     = new Rol();
            $rol->usuario_creador_id = Auth::user()->id;
            $rol->nombres             = $request->input('nombre');
            $rol->descripcion        = $request->input('descripcion');
            $rol->save();

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
    public function show(Rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rol $rol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rol $rol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rol $rol)
    {
        //
    }
}
