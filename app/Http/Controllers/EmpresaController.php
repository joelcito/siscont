<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\PuntoVenta;
use App\Models\SiatTipoDocumentoSector;
use App\Models\Sucursal;
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
            $empresa                                            = new Empresa();
            $empresa->usuario_creador_id                        = Auth::user()->id;
            $empresa->nombre                                    = $request->input('nombre_empresa');
            $empresa->nit                                       = $request->input('nit_empresa');
            $empresa->razon_social                              = $request->input('razon_social');
            $empresa->codigo_ambiente                           = $request->input('codigo_ambiente');
            $empresa->codigo_sistema                            = $request->input('codigo_sistema');
            $empresa->codigo_documento_sector                   = $request->input('documento_sectores');
            $empresa->api_token                                 = $request->input('api_token');
            $empresa->url_facturacionCodigos                    = $request->input('url_fac_codigos');
            $empresa->url_facturacionSincronizacion             = $request->input('url_fac_sincronizacion');
            $empresa->url_servicio_facturacion_compra_venta     = $request->input('url_fac_servicios');
            $empresa->url_facturacion_operaciones               = $request->input('url_fac_operaciones');
            $empresa->url_facturacionCodigos_pro                = $request->input('url_fac_codigos_pro');
            $empresa->url_facturacionSincronizacion_pro         = $request->input('url_fac_sincronizacion_pro');
            $empresa->url_servicio_facturacion_compra_venta_pro = $request->input('url_fac_servicios_pro');
            $empresa->url_facturacion_operaciones_pro           = $request->input('url_fac_operaciones_pro');

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

    public function detalle(Request $request, $empresa_id){

        $empresa            = Empresa::find($empresa_id);
        $documentosSectores = SiatTipoDocumentoSector::all();

        return view('empresa.detalle')->with(compact('empresa', 'documentosSectores'));
    }

    public function ajaxListadoSucursal(Request $request){
        if($request->ajax()){
            $data['estado']  = 'success';
            $sucursales      = Sucursal::all();
            $data['listado'] = view('empresa.ajaxListadoSucursal')->with(compact('sucursales'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardaSucursal(Request $request){
        if($request->ajax()){

            $sucursal                     = new Sucursal();
            $sucursal->usuario_creador_id = Auth::user()->id;
            $sucursal->usuario_creador_id = $request->input('empresa_id_sucursal');
            $sucursal->nombre             = $request->input('nombre_sucursal');
            $sucursal->codigo_sucursal    = $request->input('codigo_sucursal');
            $sucursal->direccion          = $request->input('direccion_sucursal');

            if($sucursal->save()){
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

    public function ajaxListadoPuntoVenta(Request $request){
        if($request->ajax()){
            $data['estado']  = 'success';

            $punto_ventas = PuntoVenta::all();

            $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        //
    }
}
