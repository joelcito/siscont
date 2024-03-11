<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\SiatTipoDocumentoSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{

    public function listado(Request $request){
        $documentosSectores = SiatTipoDocumentoSector::all();
        return view('empresa.listado')->with(compact('documentosSectores'));
    }


    /**
     * Display a listing of the resource.
     */
    public function guarda(Request $request){   
        if($request->ajax()){
            // dd($request->all());
            $empresa = new Empresa();
            $empresa->usuario_creador_id                    = Auth::user()->id;
            $empresa->nombre                                = $request->input('nombre_empresa');
            $empresa->nit                                   = $request->input('nit_empresa');
            $empresa->razon_social                          = $request->input('razon_social');
            $empresa->codigo_ambiente                       = $request->input('codigo_ambiente');
            $empresa->codigo_sistema                        = $request->input('codigo_sistema');
            $empresa->codigo_documento_sector               = $request->input('documento_sectores');
            $empresa->url_facturacionCodigos                = $request->input('url_fac_codigos');
            $empresa->url_facturacionSincronizacion         = $request->input('url_fac_sincronizacion');
            $empresa->url_servicio_facturacion_compra_venta = $request->input('url_fac_servicios');
            $empresa->url_facturacion_operaciones           = $request->input('url_fac_operaciones');

            if($empresa->save()){
                $data['estado'] = 'success';
                $data['text']   = 'Se creo con exito';
            }else{
                $data['text']   = 'Erro al crear';
                $data['estado'] = 'error';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

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
        $empresas = Empresa::all();
        return view('empresa.ajaxListado')->with(compact('empresas'))->render();
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
    public function show(Empresa $empresa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        //
    }
}
