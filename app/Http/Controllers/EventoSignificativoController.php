<?php

namespace App\Http\Controllers;

use App\Models\Cufd;
use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\EventoSignificativo;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\SiatEventoSignificativo;
use App\Models\Sucursal;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PharData;
use SimpleXMLElement;
use TypeError;

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
                                                        ->orderBy('id','desc')
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
            try {
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
                        // $cufdNew->fecha_vigencia     = $cufd->resultado->RespuestaCufd->fechaVigencia;
                        $cufdNew->fecha_vigencia     = Carbon::parse($cufd->resultado->RespuestaCufd->fechaVigencia)->format('Y-m-d H:i:s');
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

                try {
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

                            // ELIMINAMOS EL PRIMERO CREADO
                            Cufd::destroy($cufdNew->id);
                            // ELIMINAMOS EL PRIMERO CREADO
                        }
                    }else{
                        $data['estado']     = "error";
                        $data['msg']        = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;
                    }
                } catch (ErrorException $e) {
                    $data['estado']     = "error";
                    $data['msg']        = $e->getMessage();
                }

            } catch (TypeError $th) {
                $data['estado']    = 'error';
                $data['msg']       = $th->getMessage();
                $data['num_error'] = 1;
                return $data;
            }


        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function buscarEventosSignificativos(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $fecha_contingencia = $request->input('fecha_contingencia');
            $fecha_ini = $fecha_contingencia." 00:00:00";
            $fecha_fin = $fecha_contingencia." 23:59:59";

            // $eventos_significativos = EventoSignificativo::whereBetween($fecha_contingencia, ['fecha_ini_evento','fecha_fin_evento'])
            //                                             ->get();

            $eventos_significativos = EventoSignificativo::whereRaw('? BETWEEN LEFT(fecha_ini_evento, 10) AND LEFT(fecha_fin_evento, 10)', [$fecha_contingencia])->get();

            $data['estado'] = 'success';
            $data['eventos'] = $eventos_significativos;

            // dd($eventos_significativos);

            // $fechaEvento = $request->input('fecha_contingencia');
            // $siat = app(SiatController::class);
            // $respuesta = json_decode($siat->consultaEventoSignificativo($fechaEvento));
            // // dd($respuesta);
            // if($respuesta->estado === "success"){
            //     if($respuesta->resultado->RespuestaListaEventos->transaccion){
            //         $eventos = json_decode(json_encode($respuesta->resultado->RespuestaListaEventos->listaCodigos), true);
            //         $data['estado'] = 'success';
            //         $data['eventos'] = $eventos;
            //         // $data['listado'] = view('eventosignificativo.ajaxListado')->with(compact('eventos'))->render();
            //     }else{
            //         //NO EXISTE REGISTRO DE EVENTO SIGNIFICATIVO EN LA BASE DE DATOS DEL SIN
            //         // dd($respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion);
            //         $data['estado'] = 'error';
            //         $data['msg'] = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;
            //     }
            // }else{
            //     $data['estado'] = 'error';
            //     $data['msg'] = 'ERROR EN LA BASE DE DATOS O CONSULTA';
            // }

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }

        return $data;

    }

    public function muestraTableFacturaPaquete(Request $request){
        if($request->ajax()){

            $fecha                   = $request->input('fecha');
            $evento_significativo_id = $request->input('valor');
            $fecha_ini               = $fecha." 00:00:00";
            $fecha_fin               = $fecha." 23:59:59";
            $facturas                = [];

            if(($evento_significativo_id) != null){
                $evento_significativo = EventoSignificativo::find($evento_significativo_id);
                $facturas = Factura::where('tipo_factura', 'offline')
                                    ->where('facturado', "Si")
                                    ->where('cufd_id',$evento_significativo->cufd_evento_id)
                                    ->WhereNull('codigo_descripcion')
                                    ->whereBetween('fecha', [$fecha_ini, $fecha_fin])
                                    ->orderBy('id', 'desc')
                                    ->limit(500)
                                    ->get();
                $data['listado'] = view('factura.ajaxMuestraTableFacturaPaquete')->with(compact('facturas'))->render();
                $data['estado'] = "success";
            }else{
                $data['listado'] = view('factura.ajaxMuestraTableFacturaPaquete')->with(compact('facturas'))->render();
                $data['estado'] = "success";
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function mandarFacturasPaquete(Request $request){
        if($request->ajax()){

            $datos = $request->all();
            $checkboxes = collect($datos)->filter(function ($value, $key) {
                return Str::startsWith($key, 'check_');
            })->toArray();

            $evento_significativo_id = $request->input('evento_significativo_id');
            $empresa_id              = Auth::user()->empresa_id;
            $sucursal_id             = Auth::user()->sucursal_id;
            $punto_venta_id          = Auth::user()->punto_venta_id;

            $evento_significativo = EventoSignificativo::find($evento_significativo_id);
            $empresa              = Empresa::find($empresa_id);
            $sucursal             = Sucursal::find($sucursal_id);
            $punto_venta          = PuntoVenta::find($punto_venta_id);
            $cuis                 = $empresa->cuisVigente($sucursal_id,$punto_venta_id, $empresa->codigo_ambiente);

            $codigo_evento_significativo    = $evento_significativo->codigoRecepcionEventoSignificativo;
            $siat                           = app(SiatController::class);
            // $codigo_cafc_contingencia       = NULL;
            $codigo_cafc_contingencia       = $empresa->cafc;
            $fechaActual                    = date('Y-m-d\TH:i:s.v');
            $fechaEmicion                   = $fechaActual;

            $contado = 0;

            $rutaCarpeta = "assets/docs/paquete";
            // Verificar si la carpeta existe
            if (!file_exists($rutaCarpeta))
                mkdir($rutaCarpeta, 0755, true);

            // Obtener lista de archivos en la carpeta
            $archivos = glob($rutaCarpeta . '/*');
            // Eliminar cada archivo
            foreach ($archivos as $archivo) {
                if (is_file($archivo))
                    unlink($archivo);
            }
            $file = public_path('assets/docs/paquete.tar.gz');
            if (file_exists($file))
                unlink($file);

            $file = public_path('assets/docs/paquete.tar');
            if (file_exists($file))
                unlink($file);

            $idsToUpdate = [];
            foreach($checkboxes as $key => $chek){
                $ar = explode("_",$key);
                $factura = Factura::find($ar[1]);

                $idsToUpdate[] = (int)$ar[1];

                $xml                            = $factura->productos_xml;
                $archivoXML                     = new SimpleXMLElement($xml);

                // GUARDAMOS EN LA CARPETA EL XML
                $archivoXML->asXML("assets/docs/paquete/facturaxmlContingencia$ar[1].xml");
                $contado++;
            }

            // Ruta de la carpeta que deseas comprimir
            $rutaCarpeta = "assets/docs/paquete";

            // Nombre y ruta del archivo TAR resultante
            $archivoTar = "assets/docs/paquete.tar";

            // Crear el archivo TAR utilizando la biblioteca PharData
            $tar = new PharData($archivoTar);
            $tar->buildFromDirectory($rutaCarpeta);

            // Ruta y nombre del archivo comprimido en formato Gzip
            $archivoGzip = "assets/docs/paquete.tar.gz";

            // ESTE ES OTRO CHEEE
            // Abre el archivo .gz en modo de escritura
            $gz = gzopen($archivoGzip, 'wb');
            // Abre el archivo .tar en modo de lectura
            $archivo = fopen($archivoTar, 'rb');
            // Lee el contenido del archivo .tar y escribe en el archivo .gz
            while (!feof($archivo)) {
                gzwrite($gz, fread($archivo, 8192));
            }
            // Cierra los archivos
            fclose($archivo);
            gzclose($gz);

            // Leer el contenido del archivo comprimido
            $contenidoArchivo = file_get_contents($archivoGzip);

            // Calcular el HASH (SHA256) del contenido del archivo
            $hashArchivo = hash('sha256', $contenidoArchivo);


            try {

                $cufdVigente = json_decode(
                    $siat->verificarConeccion(
                        $empresa->id,
                        $sucursal->id,
                        $cuis->id,
                        $punto_venta->id,
                        $empresa->codigo_ambiente
                    ));

                $header                = $empresa->api_token;
                $url3                  = $empresa->url_servicio_facturacion_compra_venta;
                $codigoAmbiente        = $empresa->codigo_ambiente;
                $codigoDocumentoSector = $empresa->codigo_documento_sector;
                $tipo_online_o_offline = 2;                                                // FUERA DE  LINEA (LINEA = 1 | FUERA DE LINEA = 2)
                $codigoModalidad       = $empresa->codigo_modalidad;
                $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
                $codigoSistema         = $empresa->codigo_sistema;
                $codigoSucursal        = $sucursal->codigo_sucursal;
                $scufd                 = $cufdVigente->codigo;
                $scuis                 = $cuis->codigo;
                $nit                   = $empresa->nit;
                $tipoFacturaDocumento  = ($empresa->codigo_documento_sector == 8)? 2 : 1;

                // Código que puede lanzar el error
                // Por ejemplo, puedes tener algo como:
                $res = json_decode($siat->recepcionPaqueteFactura(
                    $header,
                    $url3,
                    $codigoAmbiente,
                    $codigoDocumentoSector,
                    $tipo_online_o_offline,
                    $codigoModalidad,
                    $codigoPuntoVenta,
                    $codigoSistema,
                    $codigoSucursal,
                    $scufd,
                    $scuis,
                    $nit,
                    $tipoFacturaDocumento,

                    $contenidoArchivo, $fechaEmicion, $hashArchivo, $codigo_cafc_contingencia, $contado, $codigo_evento_significativo
                ));
                if($res->resultado->RespuestaServicioFacturacion->transaccion){

                    $header                = $empresa->api_token;
                    $url3                  = $empresa->url_servicio_facturacion_compra_venta;;
                    $codigoAmbiente        = $empresa->codigo_ambiente;
                    $codigoDocumentoSector = $empresa->codigo_documento_sector;
                    $codigoModalidad       = $empresa->codigo_modalidad;
                    $codigoPuntoVenta      = $punto_venta->codigoPuntoVenta;
                    $codigoSistema         = $empresa->codigo_sistema;
                    $codigoSucursal        = $sucursal->codigo_sucursal;
                    $scufd                 = $cufdVigente->codigo;
                    $scuis                 = $cuis->codigo;
                    $nit                   = $empresa->nit;
                    $tipoFacturaDocumento  = ($empresa->codigo_documento_sector == 8)? 2 : 1;

                    $validad = json_decode($siat->validacionRecepcionPaqueteFactura(
                        $header,
                        $url3,
                        $codigoAmbiente,
                        $codigoDocumentoSector,
                        $codigoModalidad,
                        $codigoPuntoVenta,
                        $codigoSistema,
                        $codigoSucursal,
                        $scufd,
                        $scuis,
                        $nit,
                        $tipoFacturaDocumento,

                        2,$res->resultado->RespuestaServicioFacturacion->codigoRecepcion
                    ));
                    if($validad->resultado->RespuestaServicioFacturacion->transaccion){

                        $data['estado'] = "success";
                        $data['msg']    = $validad->resultado;

                        // Realizar la actualización utilizando Eloquent
                        Factura::whereIn('id', $idsToUpdate)->update([
                            'codigo_descripcion'    => $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion,
                            'codigo_recepcion'      => $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion
                        ]);
                    }else{
                        $data['estado'] = "error";
                        $data['msg']    = $validad->resultado;

                        // Realizar la actualización utilizando Eloquent
                        Factura::whereIn('id', $idsToUpdate)->update([
                            'codigo_descripcion'    => $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion,
                            'codigo_recepcion'      => $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion,
                            'descripcion'           => $validad->resultado->RespuestaServicioFacturacion->mensajesList
                        ]);
                    }
                }else{
                    // dd($res);
                    $data['estado'] = "error";
                    $data['msg']    = $res->resultado;
                }
            } catch (ErrorException $e) {
                $data['estado'] = "error";
                $data['msg']    = $e->getMessage();
            }
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        // dd($data);
        return $data;
    }
}
