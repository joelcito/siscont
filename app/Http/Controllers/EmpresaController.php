<?php

namespace App\Http\Controllers;

use App\Models\Cuis;
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
            $sucursal->nombre             = $request->input('nombre_sucursal');
            $sucursal->codigo_sucursal    = $request->input('codigo_sucursal');
            $sucursal->direccion          = $request->input('direccion_sucursal');
            $sucursal->empresa_id         = $request->input('empresa_id_sucursal');

            if($sucursal->save()){

                $punto_venta                     = new PuntoVenta();
                $punto_venta->usuario_creador_id = Auth::user()->id;
                $punto_venta->sucursal_id        = $sucursal->id;
                $punto_venta->codigoPuntoVenta   = 0;
                $punto_venta->nombrePuntoVenta   = "PRIMER PUNTO VENTA";
                $punto_venta->tipoPuntoVenta     = "VENTANILLA INICIAL";
                $punto_venta->codigo_ambiente    = 2;
                $punto_venta->save();

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

            $sucursal_id  = $request->input('sucursal');

            $punto_ventas = PuntoVenta::where('sucursal_id', $sucursal_id)
                                        ->get();

            $data['estado']  = 'success';
            $data['listado'] = view('empresa.ajaxListadoPuntoVenta')->with(compact('punto_ventas'))->render();
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function crearCuis(Request $request){
        if($request->ajax()){
            $punto_venta_id = $request->input('codigo_punto_venta_id_cuis');
            $sucursal_id    = $request->input('codigo_sucursal_id_cuis');

            $punto_venta = PuntoVenta::find($punto_venta_id);
            $sucursal    = Sucursal::find($sucursal_id);
            $empresa     = Empresa::find($sucursal->empresa_id);

            $siat = app(SiatController::class);

            $codigoCuis = json_decode($siat->cuis(
                $empresa->api_token,
                $empresa->url_facturacionCodigos,
                $empresa->codigo_ambiente,
                $empresa->codigo_modalidad,
                $punto_venta->codigoPuntoVenta,
                $empresa->codigo_sistema,
                $sucursal->codigo_sucursal,
                $empresa->nit
            ));

            // dd($codigoCuis);

            if($codigoCuis->estado === "success"){
                // dd($codigoCuis);
                // session(['scuis'                => $codigoCuis->resultado->RespuestaCuis->codigo]);
                // session(['sfechaVigenciaCuis'   => $codigoCuis->resultado->RespuestaCuis->fechaVigencia]);

                // dd($codigoCuis);

                $codigoCuisGenerado    = $codigoCuis->resultado->RespuestaCuis->codigo;
                $fechaVigenciaGenerado = $codigoCuis->resultado->RespuestaCuis->fechaVigencia;

                $cuisSacado = Cuis::where('punto_venta_id', $punto_venta->id)
                                    ->where('sucursal_id', $sucursal->id)
                                    ->where('codigo', $codigoCuisGenerado)
                                    ->first();

                if(is_null($cuisSacado)){
                    $cuis                     = new Cuis();
                    $cuis->usuario_creador_id = Auth::user()->id;
                    $cuis->punto_venta_id     = $punto_venta->id;
                    $cuis->sucursal_id        = $sucursal->id;
                    $cuis->codigo             = $codigoCuisGenerado;
                    $cuis->fechaVigencia      = $fechaVigenciaGenerado;
                    $cuis->codigo_ambiente    = $empresa->codigo_ambiente;
                    if($cuis->save()){
                        $data['text']   = 'Se creo el CUIS con exito';
                        $data['estado'] = 'success';
                    }else{
                        $data['text']   = 'Error al crear el CUIS';
                        $data['estado'] = 'error';
                    }
                }else{
                    $data['text']   = 'Ya existe un CUIS del punto de Venta y Sucursal';
                    $data['estado'] = 'warnig';
                }
                // $data['$codigoCuis->estado === "success"'] = 'si';
            }else{
                // dd("no");
                // $data['$codigoCuis->estado === "success"'] = 'no';
                $data['text']   = 'Error en la consulta';
                $data['msg']    = $codigoCuis;
                $data['estado'] = 'error';
            }
            // $data['!session()->has("scuis")'] = 'si';


            // $header                = $empresa->api_token;
            // $codigoAmbiente        = $empresa->codigo_ambiente;
            // $codigoModalidad       = $empresa->codigo_modalidad;
            // $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
            // $codigoSistema         = $empresa->codigo_sistema;
            // $codigoSucursal        = $sucursal->codigo_sucursal;
            // $nit                   = $empresa->nit;
            // $codigoDocumentoSector = "";
            // $url1                  = "";
            // $url2                  = "";
            // $url3                  = "";
            // $url4                  = "";


            // $siat = new SiatController(
            //     $header,
            //     $codigoAmbiente,
            //     $codigoModalidad,
            //     $codigoPuntoVenta,
            //     $codigoSistema,
            //     $codigoSucursal,
            //     $nit,
            //     $codigoDocumentoSector,
            //     $url1,
            //     $url2,
            //     $url3,
            //     $url4
            // );

            // dd($request->all());
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }
}
