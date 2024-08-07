<?php

namespace App\Http\Controllers;

use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\RegistroCompra;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use PharData;
use SimpleXMLElement;
use Illuminate\Support\Str;

class RegistroCompraController extends Controller
{

    // Propiedades privadas
    // private $_usuario;
    // private $_empresa;
    // private $_punto_venta;
    // private $_sucursal;
    // private $_cuis;

    // Constructor del controlador
    public function __construct()
    {
        // // dd(Auth::user());

        // // Aplica middleware a todas las rutas de este controlador
        // $this->middleware('auth');

        // $this->_usuario = Auth::user();

        // dd($this->_usuario, Auth::user());

        // if ($this->_usuario) {
        //     $this->_empresa     = Empresa::find($this->_usuario->empresa_id);
        //     $this->_punto_venta = PuntoVenta::find($this->_usuario->punto_venta_id);
        //     $this->_sucursal    = Sucursal::find($this->_usuario->sucursal_id);

        //     if ($this->_punto_venta && $this->_sucursal && $this->_empresa) {
        //         $this->_cuis = Cuis::where('punto_venta_id', $this->_punto_venta->id)
        //                         ->where('sucursal_id', $this->_sucursal->id)
        //                         ->where('codigo_ambiente', $this->_empresa->codigo_ambiente)
        //                         ->first();
        //     } else {
        //         // Manejo de caso cuando punto de venta, sucursal o empresa no se encuentran
        //         $this->_cuis = null;
        //     }
        // } else {
        //     // Manejo del caso cuando el usuario no está autenticado
        //     $this->_empresa     = null;
        //     $this->_punto_venta = null;
        //     $this->_sucursal    = null;
        //     $this->_cuis        = null;
        // }

    }

    /**
     * Display a listing of the resource.
     */
    public function listado(Request $request){
        // dd(Auth::user());
        return view('registrocompra.listado');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajaxListado(Request $request){
        if($request->ajax()){

            $usuario_id     = Auth::user()->id;
            $empresa_id     = Auth::user()->empresa_id;
            $sucursal_id    = Auth::user()->sucursal_id;
            $punto_venta_id = Auth::user()->punto_venta_id;


            // $usuario_id     = $this->_usuario->id;
            // $empresa_id     = $this->_usuario->empresa_id;
            // $sucursal_id    = $this->_usuario->sucursal_id;
            // $punto_venta_id = $this->_usuario->punto_venta_id;

            $registroCompra = Factura::where('empresa_id', $empresa_id)
                                                        ->where('punto_venta_id',$punto_venta_id)
                                                        ->where('sucursal_id',$sucursal_id)
                                                        ->where('registro_compra','Si')
                                                        ->orderBy('id','desc')
                                                        ->get();

            $data['listado'] = view('registrocompra.ajaxListado')->with(compact('registroCompra'))->render();
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function agregarRegistroCompra(Request $request){
        if($request->ajax()){

            $datos          = $request->all();
            $usuario        = Auth::user();
            $empresa_id     = $usuario->empresa_id;
            $punto_venta_id = $usuario->punto_venta_id;
            $sucursal_id    = $usuario->sucursal_id;
            $empresa        = Empresa::find($empresa_id);

            // $array = array();

            // $array = collect($datos)->filter(function ($value, $key) {
            //     return Str::startsWith($key, 'factura_');
            // })->toArray();

            // dd($array, $datos);

            // SACAMOS EL CUIS VIGENTE
            $cuis = $empresa->cuisVigente($sucursal_id, $punto_venta_id, $empresa->codigo_ambiente);

            $siat = app(SiatController::class);

            $cufdVigente = json_decode(
                        $siat->verificarConeccion(
                            $empresa_id,
                            $sucursal_id,
                            $cuis->id,
                            $punto_venta_id,
                            $empresa->codigo_ambiente
                        ));

            $datos['nro']               = 0;
            $datos['razonSocialEmisor'] = 'VIRUSNOT SYSTEM S.R.L.';

            $factura                          = new Factura();
            $factura->usuario_creador_id      = $usuario->id;
            $factura->empresa_id              = $empresa_id;
            $factura->sucursal_id             = $sucursal_id;
            $factura->punto_venta_id          = $punto_venta_id;
            $factura->cufd_id                 = $cufdVigente->id;
            $factura->registro_compra         = 'Si';
            $factura->nit                     = $request->input('nitEmisor');
            $factura->razon_social            = $request->input('razonSocialEmisor');
            $factura->numero_factura          = $request->input('numeroFactura');
            $factura->fecha                   = $request->input('fechaEmision');
            $factura->total                   = $request->input('montoTotalCompra');
            $factura->descuento_adicional     = $request->input('descuento');
            $factura->monto_total_subjeto_iva = $request->input('montoTotalSujetoIva');
            // $factura->productos_xml           = file_get_contents('assets/docs/paqueteCompras/registroCompra.xml');
            $factura->productos_xml           = json_encode($datos);
            $factura->save();

            $numeroFacturaEmpresa = $factura->id;
            // $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));


            /*

            $xml = new \SimpleXMLElement('<registroCompra/>');

            // Añadir los elementos al XML de manera iterativa
            foreach ($datos as $key => $value) {
                // if ($key == 'fechaEmision') {
                //     $value .= 'T00:00:00';
                // }
                $xml->addChild($key, $value);
            }

            // Convertir el objeto SimpleXMLElement a una cadena XML
            $xmlString = $xml->asXML();

            // Reemplazar la declaración XML por la deseada
            $xmlString = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', $xmlString);

            $rutaCarpeta = "assets/docs/paqueteCompras";
            // Verificar si la carpeta existe
            if (!file_exists($rutaCarpeta))
                mkdir($rutaCarpeta, 0755, true);

            // Guardar el contenido XML en un archivo
            $filePath = public_path('assets/docs/paqueteCompras/registroCompra.xml');
            file_put_contents($filePath, $xmlString);

            $factura->productos_xml           = file_get_contents('assets/docs/paqueteCompras/registroCompra.xml');
            $factura->save();


            */
            $data['text']   = 'Se registro con exito';
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function numeroFactura($empresa_id, $sucursal_id, $punto_venta_id){
        // $numeroFactura = Factura::where('empresa_id', $empresa_id)
        //                         ->where('sucursal_id', $sucursal_id)
        //                         ->where('punto_venta_id', $punto_venta_id)
        //                         ->max('numero_factura');

        $numeroFactura = Factura::where('empresa_id', $empresa_id)
                                ->where('sucursal_id', $sucursal_id)
                                ->where('punto_venta_id', $punto_venta_id)
                                ->where('registro_compra', 'Si')
                                ->selectRaw('MAX(CAST(numero_factura AS UNSIGNED)) as numero_factura')
                                ->pluck('numero_factura')
                                ->first();

        return $numeroFactura;
    }

    /**
     * Display the specified resource.
     */
    public function ajaxListadoRecepcion(Request $request){
        if($request->ajax()){

            $facturas = Factura::where('registro_compra', 'Si')
                                ->whereNull('codigo_recepcion')
                                ->get();

            $data['listado']   = view('registrocompra.ajaxListadoRecepcion')
                                ->with(compact('facturas'))
                                ->render();
            $data['estado'] = 'success';
        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function envioPaquetesFacturasCompra(Request $request){
        if($request->ajax()){

            $usuario        = Auth::user();
            $empresa_id     = $usuario->empresa_id;
            $punto_venta_id = $usuario->punto_venta_id;
            $sucursal_id    = $usuario->sucursal_id;

            $empresa_objeto     = Empresa::find($empresa_id);
            $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
            $sucursal_objeto    = Sucursal::find($sucursal_id);

            $cuis_objeto       = Cuis::where('punto_venta_id', $punto_venta_objeto->id)
                                ->where('sucursal_id', $sucursal_objeto->id)
                                ->where('codigo_ambiente', $empresa_objeto->codigo_ambiente)
                                ->first();

            $siat = app(SiatController::class);

            $cufdVigente = json_decode(
                $siat->verificarConeccion(
                    $empresa_objeto->id,
                    $sucursal_objeto->id,
                    $cuis_objeto->id,
                    $punto_venta_objeto->id,
                    $empresa_objeto->codigo_ambiente
                ));

            $datos = $request->all();

            $url5             = $empresa_objeto->url_recepcion_compras;
            $header           = $empresa_objeto->api_token;
            $codigoAmbiente   = $empresa_objeto->codigo_ambiente;
            $codigoPuntoVenta = $punto_venta_objeto->codigoPuntoVenta;
            $codigoSistema    = $empresa_objeto->codigo_sistema;
            $codigoSucursal   = $sucursal_objeto->codigo_sucursal;
            $cufd             = $cufdVigente->codigo;
            $cuis             = $cuis_objeto->codigo;
            $nit              = $empresa_objeto->nit;
            $fecha            = date('Y-m-d\TH:i:s.v');

            $fechaActual                    = date('Y-m-d\TH:i:s.v');
            $fechaEmicion                   = $fechaActual;

            //  ESTE ESTA BIEN
            $rutaCarpeta = "assets/docs/paqueteCompras/paquete";
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
            $file = public_path('assets/docs/paqueteCompras/paquete.tar.gz');
            if (file_exists($file))
                unlink($file);

            $file = public_path('assets/docs/paqueteCompras/paquete.tar');
            if (file_exists($file))
                unlink($file);

            $idsToUpdate      = [];
            $resultados       = [];
            $cantidadFacturas = 0;

            $datos = $request->all();
            $checkboxes = collect($datos)->filter(function ($value, $key) {
                return Str::startsWith($key, 'factura_');
            })->toArray();


            $idsToUpdate = [];
            foreach($checkboxes as $key => $chek){

                $cantidadFacturas++;

                $ar            = explode("_",$key);
                $factura       = Factura::find($ar[1]);
                $idsToUpdate[] = (int)$ar[1];
                $xml           = $factura->productos_xml;

                // Decode the JSON string into a PHP associative array
                $array = json_decode($xml, true);
                $array['nro'] = $cantidadFacturas;


                $xml = new \SimpleXMLElement('<registroCompra/>');

                // Añadir los elementos al XML de manera iterativa
                foreach ($array as $key => $value)
                    $xml->addChild($key, $value);

                // Convertir el objeto SimpleXMLElement a una cadena XML
                $xmlString = $xml->asXML();

                // Reemplazar la declaración XML por la deseada
                $xmlString = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', $xmlString);

                $rutaCarpeta = "assets/docs/paqueteCompras/paquete";
                // Verificar si la carpeta existe
                if (!file_exists($rutaCarpeta))
                    mkdir($rutaCarpeta, 0755, true);

                // Guardar el contenido XML en un archivo
                $filePath = public_path("assets/docs/paqueteCompras/paquete/facturaxmlCompras_$ar[1].xml");
                file_put_contents($filePath, $xmlString);

            }

            // Ruta de la carpeta que deseas comprimir
            $rutaCarpeta = "assets/docs/paqueteCompras/paquete";

            // Nombre y ruta del archivo TAR resultante
            $archivoTar = "assets/docs/paqueteCompras/paquete.tar";

            // ******************* ESTE SIRVE *******************
            // Crear el archivo TAR utilizando la biblioteca PharData
            $tar = new PharData($archivoTar);
            $tar->buildFromDirectory($rutaCarpeta);

            // Ruta y nombre del archivo comprimido en formato Gzip
            $archivoGzip = "assets/docs/paqueteCompras/paquete.tar.gz";

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

            // ******************* ESTE SIRVE *******************


            // // ******************* ESTE SIRVE *******************
            // Leer el contenido del archivo comprimido
            $contenidoArchivo = file_get_contents($archivoGzip);
            // Calcular el HASH (SHA256) del contenido del archivo
            $hashArchivo = hash('sha256', $contenidoArchivo);
            // // ******************* ESTE SIRVE *******************


            $gestion = 2024;
            $periodo = 8;

            $consultaCompras = json_decode(
                $siat->recepcionPaqueteCompras(
                    $url5,
                    $header,
                    $codigoAmbiente,
                    $codigoPuntoVenta,
                    $codigoSistema,
                    $codigoSucursal,
                    $cufd,
                    $cuis,
                    $nit,

                    $contenidoArchivo,
                    $cantidadFacturas,
                    $fechaEmicion,
                    $gestion,
                    $hashArchivo,
                    $periodo
                ));


            if($consultaCompras->estado == 'success'){

                // dd($consultaCompras->resultado->RespuestaServicioFacturacion->transaccion);
                if($consultaCompras->resultado->RespuestaServicioFacturacion->transaccion){
                    // Realizar la actualización utilizando Eloquent

                    Factura::whereIn('id', $idsToUpdate)->update([
                        'codigo_descripcion'    => $consultaCompras->resultado->RespuestaServicioFacturacion->codigoDescripcion,
                        'codigo_recepcion'      => $consultaCompras->resultado->RespuestaServicioFacturacion->codigoRecepcion
                    ]);

                    $validacionRecepcionPaqueteCompras = json_decode(
                        $siat->validacionRecepcionPaqueteCompras(
                            $url5,
                            $header,
                            $codigoAmbiente,
                            $codigoPuntoVenta,
                            $codigoSistema,
                            $codigoSucursal,
                            $cufd,
                            $cuis,
                            $nit,
                            $consultaCompras->resultado->RespuestaServicioFacturacion->codigoRecepcion
                        ));

                    // $confirmacionCompras = json_decode(
                    //     $siat->confirmacionCompras(
                    //         $url5,
                    //         $header,
                    //         $codigoAmbiente,
                    //         $codigoPuntoVenta,
                    //         $codigoSistema,
                    //         $codigoSucursal,
                    //         $cufd,
                    //         $cuis,
                    //         $nit,

                    //         $contenidoArchivo,
                    //         $cantidadFacturas,
                    //         $fechaEmicion,
                    //         $gestion,
                    //         $hashArchivo,
                    //         $periodo
                    //     ));

                    $fecha           = '2024-08-06';
                    $codAutorizacion = 0;
                      // $nitProveedor    = '1111111010';
                    $nitProveedor = '527898027';
                    $nroDuiDim    = 0;
                    $nroFactura   = 3;

                    $consultaCompras1 = json_decode(
                        $siat->consultaCompras(
                            $url5,
                            $header,
                            $codigoAmbiente,
                            $codigoPuntoVenta,
                            $codigoSistema,
                            $codigoSucursal,
                            $cufd,
                            $cuis,
                            $nit,
                            $fecha
                        ));



                    // $anulacionCompra = json_decode(
                    //     $siat->anulacionCompra(
                    //         $url5,
                    //         $header,
                    //         $codigoAmbiente,
                    //         $codigoPuntoVenta,
                    //         $codigoSistema,
                    //         $codigoSucursal,
                    //         $cufd,
                    //         $cuis,
                    //         $nit,

                    //         $codAutorizacion,
                    //         $nitProveedor,
                    //         $nroDuiDim,
                    //         $nroFactura
                    //     ));

                    dd(
                        $consultaCompras,
                        $validacionRecepcionPaqueteCompras,
                        // $confirmacionCompras,
                        $consultaCompras1
                    );

                }else{

                }
            }else{

            }

            $data['text']   = 'Se registro con exito';
            $data['estado'] = 'success';

        }else{
            $data['text']   = 'No existe';
            $data['estado'] = 'error';
        }
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistroCompra $registroCompra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistroCompra $registroCompra)
    {
        //
    }
}
