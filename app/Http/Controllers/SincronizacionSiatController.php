<?php

namespace App\Http\Controllers;

use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\PuntoVenta;
use App\Models\SiatTipoDocumentoSector;
use App\Models\SiatTipoPuntoVenta;
use App\Models\SiatUnidadMedida;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Termwind\render;

class SincronizacionSiatController extends Controller
{

    // protected $header ;
    // protected $codigoAmbiente ;
    // protected $codigoModalidad ;
    // protected $codigoPuntoVenta ;
    // protected $codigoSistema ;
    // protected $codigoSucursal ;
    // protected $nit ;
    // protected $codigoDocumentoSector ;
    // protected $url1 ;
    // protected $url2 ;
    // protected $url3 ;
    // protected $url4 ;

    public function __construct(){

    }

    public function listado(Request $request){
        return view('siat.listado');
    }

    public function ajaxListadoTipoDocumentoSector(Request $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $documentosSectores = SiatTipoDocumentoSector::all();
            $data['listado']    = view('siat.ajaxListadoTipoDocumentoSector')->with(compact('documentosSectores'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoTipoPuntoVenta(Request $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $documentosSectores = SiatTipoPuntoVenta::all();
            $data['listado']    = view('siat.ajaxListadoTipoPuntoVenta')->with(compact('documentosSectores'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sincronizarTipoDocumentoSector(Request $request){
        if($request->ajax()){

            $empresa_id = $request->input('empresa_id');

            $empresa    = Empresa::find($empresa_id);

            $sucursal   = Sucursal::where('empresa_id', $empresa_id)
                                    ->first();

            $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                    ->first();

            $cuis       = Cuis::where('punto_venta_id', $puntoVenta->id)
                              ->where('sucursal_id', $sucursal->id)
                              ->where('codigo_ambiente', $empresa->codigo_ambiente)
                              ->first();

            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoDocumentoSector(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $tipoDocuemnetoSector = SiatTipoDocumentoSector::where('codigo_clasificador', $value->codigoClasificador)->first();
                    if($tipoDocuemnetoSector){
                        $tipoDocuemnetoSector->descripcion            = $value->descripcion;
                        $tipoDocuemnetoSector->usuario_modificador_id = Auth::user()->id;
                    }else{
                        $tipoDocuemnetoSector                      = new SiatTipoDocumentoSector();
                        $tipoDocuemnetoSector->usuario_creador_id  = Auth::user()->id;
                        $tipoDocuemnetoSector->codigo_clasificador = $value->codigoClasificador;
                        $tipoDocuemnetoSector->descripcion         = $value->descripcion;
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

    public function sincronizarParametricaTipoPuntoVenta(Request $request){
        if($request->ajax()){

            $empresa_id = $request->input('empresa_id');

            $empresa    = Empresa::find($empresa_id);

            $sucursal   = Sucursal::where('empresa_id', $empresa_id)
                                    ->first();

            $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                    ->first();

            $cuis       = Cuis::where('punto_venta_id', $puntoVenta->id)
                              ->where('sucursal_id', $sucursal->id)
                              ->where('codigo_ambiente', $empresa->codigo_ambiente)
                              ->first();

            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoPuntoVenta(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $tipoPuntoVenta = SiatTipoPuntoVenta::where('codigo_clasificador', $value->codigoClasificador)->first();
                    if($tipoPuntoVenta){
                        $tipoPuntoVenta->descripcion            = $value->descripcion;
                        $tipoPuntoVenta->usuario_modificador_id = Auth::user()->id;
                    }else{
                        $tipoPuntoVenta                      = new SiatTipoPuntoVenta();
                        $tipoPuntoVenta->usuario_creador_id  = Auth::user()->id;
                        $tipoPuntoVenta->codigo_clasificador = $value->codigoClasificador;
                        $tipoPuntoVenta->descripcion         = $value->descripcion;
                    }
                    $tipoPuntoVenta->save();
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

    public function  ajaxListadoUnidadMedida(Request $request) {
        if($request->ajax()){
            $data['estado']     = 'success';
            $unidadesMedidas = SiatUnidadMedida::all();
            $data['listado']    = view('siat.ajaxListadoUnidadMedida')->with(compact('unidadesMedidas'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function  sincronizarUnidadMedida(Request $request) {
        if($request->ajax()){

            $empresa_id = $request->input('empresa_id');

            $empresa    = Empresa::find($empresa_id);

            $sucursal   = Sucursal::where('empresa_id', $empresa_id)
                                    ->first();

            $puntoVenta = PuntoVenta::where('sucursal_id', $sucursal->id)
                                    ->first();

            $cuis       = $empresa->cuisVigente($sucursal->id, $puntoVenta->id, $empresa->codigo_ambiente);

            $siat = app(SiatController::class);
            $sincronizarUnidadMedida   = json_decode($siat->sincronizarParametricaUnidadMedida(
                $empresa->api_token,
                $empresa->url_facturacionSincronizacion,
                $empresa->codigo_ambiente,
                $puntoVenta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $cuis->codigo,
                $empresa->nit
            ));

            if($sincronizarUnidadMedida->estado === "success"){
                if($sincronizarUnidadMedida->resultado->RespuestaListaParametricas->transaccion){
                    $listaCodigos = $sincronizarUnidadMedida->resultado->RespuestaListaParametricas->listaCodigos;

                    foreach ($listaCodigos as $key => $value) {
                        $unidad = SiatUnidadMedida::where('codigo_clasificador', $value->codigoClasificador)
                                                    ->first();

                        if(is_null($unidad)){
                            $unidad                      = new SiatUnidadMedida();
                            $unidad->usuario_creador_id  = Auth::user()->id;
                            $unidad->codigo_clasificador = $value->codigoClasificador;
                            $unidad->descripcion         = $value->descripcion;
                        }else{
                            $unidad->usuario_modificador_id  = Auth::user()->id;
                            $unidad->descripcion         = $value->descripcion;
                        }
                        $unidad->save();
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

            // dd($empresa_id, $sincronizarUnidadMedida);

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

}
