<?php

namespace App\Http\Controllers;

use App\Models\SiatTipoDocumentoSector;
use Illuminate\Http\Request;

use function Termwind\render;

class SincronizacionSiatController extends Controller
{

    protected $siatController;

    public function __construct(SiatController $siatController)
    {
        $this->siatController = $siatController;
    }

    public function listado(Request $request){
        // return view('siat.sincronizacion_catalogo.listado')->with(compact('documentosSectores'));
        return view('siat.sincronizacion_catalogo.listado');
    }

    public function ajaxListadoTipoDocumentoSector(Request $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $documentosSectores = SiatTipoDocumentoSector::all();
            $data['listado']    = view('siat.sincronizacion_catalogo.ajaxListadoTipoDocumentoSector')->with(compact('documentosSectores'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sincronizarTipoDocumentoSector(Request $request){
        if($request->ajax()){
            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoDocumentoIdentidad());
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $tipoDocuemnetoSector = SiatTipoDocumentoSector::where('codigo_sin', $value->codigoClasificador)->first();
                    if($tipoDocuemnetoSector){
                        $tipoDocuemnetoSector->nombre = $value->descripcion;
                    }else{
                        $tipoDocuemnetoSector             = new SiatTipoDocumentoSector();
                        $tipoDocuemnetoSector->codigo_sin = $value->codigoClasificador;
                        $tipoDocuemnetoSector->nombre     = $value->descripcion;
                    }
                    $tipoDocuemnetoSector->save();
                }
                $data['estado'] = 'success';
                $data['msg']    = 'SINCRONIZACION EXITOSA!';
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR AL SINCRONIZAR!';
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }
}
