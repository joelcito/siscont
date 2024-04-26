<?php

namespace App\Http\Controllers;

use App\Models\Cufd;
use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\EventoSignificativo;
use App\Models\PuntoVenta;
use App\Models\SiatEventoSignificativo;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoSignificativoController extends Controller
{
    public function listado(Request $request){

        $siat_evento_significativos = SiatEventoSignificativo::all();

        return view('evento_significativo.listado')->with(compact('siat_evento_significativos'));
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){

            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;

            $eventosSignificativos = EventoSignificativo::where('empresa_id', $empresa_id)
                                                        ->where('punto_venta_id',$punto_venta_id)
                                                        ->where('sucursal_id',$sucursal_id)
                                                        ->get();
        //                                                 ->toSql();
        //     dd($eventosSignificativos, 
        //     "usuario_id => ".$usuario_id,
        //     "empresa_id => ".$empresa_id,
        //     "sucursal_id => ".$sucursal_id,
        //     "punto_venta_id => ".$punto_venta_id
        // );

            $data['listado'] = view('evento_significativo.ajaxListado')->with(compact('eventosSignificativos'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function buscarCufd(Request $request){
        if($request->ajax()){

            // dd(Auth::user());

            $usuario = Auth::user();

            $empresa_id     = $usuario->empresa_id;
            $sucursal_id    = $usuario->sucursal_id;
            $punto_venta_id = $usuario->punto_venta_id;

            $empresa = Empresa::find($empresa_id);

            $cufdOffLine = $this->sacarCufdVigenteFueraLinea($empresa_id, $sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

            if($cufdOffLine['estado'] == "success"){

                $data['estado'] = 'success';

                $iniselect = '<select name="cufd_id" id="cufd_id" class="form-control" required>
                                        <option value="">Seleccione</option>';

                // $iniselect = '<select name="codigoMotivoAnulacion" id="codigoMotivoAnulacion" class="form-control" required>';

                $option = '<option value="'.$cufdOffLine['scufd_id'].'">'.$cufdOffLine['sfechaVigenciaCufd'].' | '.$cufdOffLine['scufd'].'</option>';

                $finselect = '</select>';

                $data['select'] = $iniselect.$option.$finselect;
                // $data['select'] = $option;

            }else{
                $data['text']   = $cufdOffLine['msg'];
                $data['estado'] = 'error';
            }

            // dd($cufdOffLine);

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sacarCufdVigenteFueraLinea($empresa_id,$sucursal_id,$punto_venta_id,$codigo_ambiente){

        $cufd           = Cufd::where('empresa_id', $empresa_id)
                                ->where('sucursal_id',$sucursal_id)
                                ->where('punto_venta_id',$punto_venta_id)
                                ->where('codigo_ambiente',$codigo_ambiente)
                                // ->orderBy('id','desc')->take(1)->get();
                                ->orderBy('id','desc')->first();

        // dd($cufd);


        $sw             = true;
        $tam            = 0;
        $fechaActual    = Carbon::now();

        // while($sw && $tam <= 2){
            // $fechaVigencia      = Carbon::parse($cufd[$tam]->fechaVigencia);
            $fechaVigencia      = Carbon::parse($cufd->fechaVigencia);
            $fechaLimite        = $fechaVigencia->addHours(72);
            $fechaVerificar     = $fechaActual;
            if($fechaVerificar->lte($fechaLimite)){
                $data['estado']             = "success";
                $data['scufd_id']           = $cufd->id;
                $data['scufd']              = $cufd->codigo;
                $data['scodigoControl']     = $cufd->codigo_control;
                $data['sdireccion']         = $cufd->direccion;
                $data['sfechaVigenciaCufd'] = $cufd->fecha_vigencia;
                $sw                         = false;
            }else{
                // echo "<->".$tam."<->";
                $data['estado']         = "error" ;
                $data['msg']            = "la fecha esta fuera de las 72 horas " ;
            }
            // $tam++;
        // }
        // if($sw){
        //     $data['estado']         = "error" ;
        //     $data['msg']            = "la fecha esta fuera de las 72 horas " ;
        // }
        return $data;
    }

    public function  agregarEventoSignificativo(Request $request){
        if($request->ajax()){

            // dd($request->all());


            // RECUPERAMOS EL ULTIMO CUFD QUE FUE VIGENTE
            // $cufd   = app(CufdController::class);
            // $datosCufdOffLine  = $cufd->sacarCufdVigenteFueraLinea();

            // ELIMINAMOS EL CUFD Y CREAMOS OTRO PARA EL REGISTRO DE UN NUEVO EVENTOS SIGNIFICACITO
            // session()->forget(['scufd','scodigoControl','sdireccion','sfechaVigenciaCufd']);

            $codigo_tipo_evento = $request->input('codigo_tipo_evento');
            $descripcion        = $request->input('descripcion');
            $fecha_inicio       = $request->input('fecha_inicio');
            $hora_inicio        = $request->input('hora_inicio');
            $fecha_fin          = $request->input('fecha_fin');
            $hora_fin           = $request->input('hora_fin');
            $cufd_id            = $request->input('cufd_id');

            $cufdOffLine               = Cufd::find($cufd_id);
            $siat_evento_significativo = SiatEventoSignificativo::find($codigo_tipo_evento);

            $empresa_id     = $cufdOffLine->empresa_id;
            $sucursal_id    = $cufdOffLine->sucursal_id;
            $punto_venta_id = $cufdOffLine->punto_venta_id;
            $cuis_id        = $cufdOffLine->cuis_id;

            $empresa     = Empresa::find($empresa_id);
            $punto_venta = PuntoVenta::find($punto_venta_id);
            $sucursal    = Sucursal::find($sucursal_id);
            $cuis        = Cuis::find($cuis_id);

            $codMotEvent    = $siat_evento_significativo->codigo_clasificador;
            $cufdEvent      = $cufdOffLine->codigo;
            $desc           = $descripcion;

            // $fechaIni       = $request->input('fechainicio').":00";
            // $fechaFin       = $request->input('fechafin').":00";
            // $fechaIni       = str_replace(' ', 'T', trim($request->input('fechainicio')));
            // $fechaFin       = str_replace(' ', 'T', trim($request->input('fechafin')));
            
            $fechaIni       = $fecha_inicio."T".$hora_inicio;
            $fechaFin       = $fecha_fin."T".$hora_fin;

            $header           = $empresa->api_token;
            $url1             = $empresa->url_facturacionCodigos;
            $codigoAmbiente   = $empresa->codigo_ambiente;
            $codigoModalidad  = $empresa->codigo_modalidad;
            $codigoPuntoVenta = $punto_venta->codigoPuntoVenta;
            $codigoSistema    = $empresa->codigo_sistema;
            $codigoSucursal   = $sucursal->codigo_sucursal;
            $scuis            = $cuis->codigo;
            $nit              = $empresa->nit;

            $siat = app(SiatController::class);
            // AQUI CREAMOS EL NUEVO CUFD PARA EL NUEVO DIA
            $cufd = json_decode($siat->cufd(
                $header,
                $url1,
                $codigoAmbiente,
                $codigoModalidad,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $scuis,
                $nit 
            ));
            if($cufd->estado === "success"){
                if($cufd->resultado->RespuestaCufd->transaccion){

                    $cufdNew                     = new Cufd();
                    $cufdNew->usuario_creador_id = Auth::user()->id;
                    $cufdNew->empresa_id         = $empresa_id;
                    $cufdNew->sucursal_id        = $sucursal_id;
                    $cufdNew->cuis_id            = $cuis_id;
                    $cufdNew->punto_venta_id     = $punto_venta_id;
                    $cufdNew->codigo_ambiente    = $codigoAmbiente;
                    $cufdNew->codigo             = $cufd->resultado->RespuestaCufd->codigo;
                    $cufdNew->codigo_control     = $cufd->resultado->RespuestaCufd->codigoControl;
                    $cufdNew->direccion          = $cufd->resultado->RespuestaCufd->direccion;
                    $cufdNew->fecha_vigencia     = $cufd->resultado->RespuestaCufd->fechaVigencia;
                    $cufdNew->save();
                    $cufdRescatadoUtilizar =  $cufdNew;

                    // session(['scufd' => $cufd->resultado->RespuestaCufd->codigo]);
                    // session(['scodigoControl' => $cufd->resultado->RespuestaCufd->codigoControl]);
                    // session(['sdireccion' => $cufd->resultado->RespuestaCufd->direccion]);
                    // session(['sfechaVigenciaCufd' => $cufd->resultado->RespuestaCufd->fechaVigencia]);
                    // $cufdNew = app(CufdController::class);
                    // $cufdNew->create(
                    //                 $cufd->resultado->RespuestaCufd->codigo,
                    //                 $cufd->resultado->RespuestaCufd->codigoControl,
                    //                 $cufd->resultado->RespuestaCufd->direccion,
                    //                 $cufd->resultado->RespuestaCufd->fechaVigencia,
                    //                 Auth::user()->codigo_punto_venta
                    //             );
                }
            }

            $header           = $empresa->api_token;
            $url4             = $empresa->url_facturacion_operaciones;
            $codigoAmbiente   = $empresa->codigo_ambiente;
            $codigoPuntoVenta = $punto_venta->codigoPuntoVenta;
            $codigoSistema    = $empresa->codigo_sistema;
            $codigoSucursal   = $sucursal->codigo_sucursal;

            $cufdVigente = json_decode(
                $siat->verificarConeccion(
                    $empresa->id,
                    $sucursal->id,
                    $cuis->id,
                    $punto_venta->id,
                    $empresa->codigo_ambiente
                ));

            $scufd            = $cufdVigente->codigo;
            $scuis            = $cuis->codigo;
            $nit              = $empresa->nit;

            // END AQUI CREAMOS EL NUEVO CUFD PARA EL NUEVO DIA
            $respuesta = json_decode($siat->registroEventoSignificativo(
                $header,
                $url4,
                $codigoAmbiente,
                $codigoPuntoVenta,
                $codigoSistema,
                $codigoSucursal,
                $scufd,
                $scuis,
                $nit,

                $codMotEvent, $cufdEvent, $desc, $fechaIni, $fechaFin
            ));

            if($respuesta->estado === "success"){
                if($respuesta->resultado->RespuestaListaEventos->transaccion){

                    $evento_significativo                                     = new EventoSignificativo();
                    $evento_significativo->usuario_creador_id                 = Auth::user()->id;
                    $evento_significativo->empresa_id                         = $empresa->id;
                    $evento_significativo->siat_evento_significativo_id       = $siat_evento_significativo->id;
                    $evento_significativo->punto_venta_id                     = $punto_venta->id;
                    $evento_significativo->sucursal_id                        = $sucursal->id;
                    $evento_significativo->cufd_activo_id                     = $cufdVigente->id;
                    $evento_significativo->cufd_evento_id                     = $cufdOffLine->id;
                    $evento_significativo->cuis_id                            = $cuis->id;
                    $evento_significativo->descripcion                        = $descripcion;
                    $evento_significativo->fecha_ini_evento                   = $fechaIni;
                    $evento_significativo->fecha_fin_evento                   = $fechaFin;
                    $evento_significativo->codigoRecepcionEventoSignificativo = $respuesta->resultado->RespuestaListaEventos->codigoRecepcionEventoSignificativo;
                    
                    $evento_significativo->save();

                    $data['estado']     = "success";
                    $data['msg']        = $respuesta->resultado->RespuestaListaEventos->codigoRecepcionEventoSignificativo;
                }else{
                    $data['estado']     = "error";
                    $data['msg']        = $respuesta->resultado->RespuestaListaEventos;
                }
            }else{

            //     dd( 
            //     $respuesta, 
            
            //     $header,
            //     $url4,
            //     $codigoAmbiente,
            //     $codigoPuntoVenta,
            //     $codigoSistema,
            //     $codigoSucursal,
            //     $scufd,
            //     $scuis,
            //     $nit,

            //     $codMotEvent, $cufdEvent, $desc, $fechaIni, $fechaFin
            // );

                $data['estado']     = "error";
                $data['msg']        = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;

            }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }
}
