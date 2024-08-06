<?php

namespace App\Http\Controllers;

use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\PuntoVenta;
use App\Models\RegistroCompra;
use App\Models\Sucursal;
use Illuminate\Http\Request;
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

            $datos = $request->all();

            $usuario        = Auth::user();
            $empresa_id     = $usuario->empresa_id;
            $punto_venta_id = $usuario->punto_venta_id;
            $sucursal_id    = $usuario->sucursal_id;

            $empresa     = Empresa::find($empresa_id);
            // $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
            // $sucursal_objeto    = Sucursal::find($sucursal_id);

            $numeroFacturaEmpresa = $this->numeroFactura($empresa_id, $sucursal_id, $punto_venta_id);
            $numeroFacturaEmpresa = ($numeroFacturaEmpresa == null? 1 : ($numeroFacturaEmpresa+1));

            $datos['nro']               = $numeroFacturaEmpresa;
            $datos['razonSocialEmisor'] = 'VIRUSNOT SYSTEM S.R.L.';

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
            $factura->productos_xml           = file_get_contents('assets/docs/paqueteCompras/registroCompra.xml');
            $factura->save();

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
            // $rutaCarpeta = "assets/docs/paqueteCompras/envioPaqueteCompras";
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
            // $file = public_path('assets/docs/paqueteCompras/envioPaqueteCompras.tar.gz');
            $file = public_path('assets/docs/paqueteCompras/paquete.tar.gz');
            if (file_exists($file))
                unlink($file);

            // $file = public_path('assets/docs/paqueteCompras/envioPaqueteCompras.tar');
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

                // $ar = explode("_", $key);
                // $factura = Factura::find($ar[1]);
                // if (!$factura) {
                //     dd('Error: Factura no encontrada para ID ' . $ar[1]);
                // }

                // $idsToUpdate[] = (int)$ar[1];
                // $xml = $factura->productos_xml;

                // // Verificar si el XML es válido
                // libxml_use_internal_errors(true);
                // $archivoXML = new SimpleXMLElement($xml);
                // if ($archivoXML === false) {
                //     $errors = libxml_get_errors();
                //     foreach ($errors as $error) {
                //         echo "Error de XML: " . $error->message;
                //     }
                //     libxml_clear_errors();
                //     dd('Error: XML mal formado para la factura ' . $ar[1]);
                // }

                // // Guardar el archivo XML
                // $rutaArchivo = "assets/docs/paqueteCompras/envioPaqueteCompras/facturaxmlCompras_$ar[1].xml";
                // $archivoXML->asXML($rutaArchivo);

                // // Verificar que el archivo se haya guardado correctamente
                // if (!file_exists($rutaArchivo)) {
                //     dd('Error: No se pudo guardar el archivo XML para la factura ' . $ar[1]);
                // }

                // $cantidadFacturas++;



                $ar = explode("_",$key);
                $factura = Factura::find($ar[1]);

                $idsToUpdate[] = (int)$ar[1];

                $xml                            = $factura->productos_xml;
                // $uso_cafc                       = $request->input("uso_cafc");
                $archivoXML                     = new SimpleXMLElement($xml);

                // GUARDAMOS EN LA CARPETA EL XML
                // $archivoXML->asXML("assets/docs/paqueteCompras/envioPaqueteCompras/facturaxmlCompras_$ar[1].xml");
                $archivoXML->asXML("assets/docs/paqueteCompras/paquete/facturaxmlCompras_$ar[1].xml");
                $cantidadFacturas++;
            }

            // Ruta de la carpeta que deseas comprimir
            // $rutaCarpeta = "assets/docs/paqueteCompras/envioPaqueteCompras";
            $rutaCarpeta = "assets/docs/paqueteCompras/paquete";

            // Nombre y ruta del archivo TAR resultante
            // $archivoTar = "assets/docs/paqueteCompras/envioPaqueteCompras.tar";
            $archivoTar = "assets/docs/paqueteCompras/paquete.tar";



            // // Crear el archivo TAR utilizando la biblioteca PharData
            // $tar = new PharData($archivoTar);
            // $tar->buildFromDirectory($rutaCarpeta);

            // // Ruta y nombre del archivo comprimido en formato Gzip
            // $archivoGzip = "assets/docs/paqueteCompras/envioPaqueteCompras.tar.gz";

            // // Abre el archivo .gz en modo de escritura
            // $gz = gzopen($archivoGzip, 'wb');
            // // Abre el archivo .tar en modo de lectura
            // $archivo = fopen($archivoTar, 'rb');
            // if ($archivo === false) {
            //     dd('Error: No se puede abrir el archivo TAR para lectura');
            // }

            // // Lee el contenido del archivo .tar y escribe en el archivo .gz
            // while (!feof($archivo)) {
            //     gzwrite($gz, fread($archivo, 8192));
            // }

            // // Cierra los archivos
            // fclose($archivo);
            // gzclose($gz);

            // // Verificar que el archivo gzip se haya creado correctamente
            // if (!file_exists($archivoGzip)) {
            //     dd('Error: No se pudo crear el archivo Gzip');
            // }


            // ******************* ESTE SIRVE *******************
            // Crear el archivo TAR utilizando la biblioteca PharData
            $tar = new PharData($archivoTar);
            $tar->buildFromDirectory($rutaCarpeta);

            // Ruta y nombre del archivo comprimido en formato Gzip
            // $archivoGzip = "assets/docs/paqueteCompras/envioPaqueteCompras.tar.gz";
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


            // // Leer el contenido del archivo comprimido
            // $contenidoArchivo = file_get_contents($archivoGzip);
            // if ($contenidoArchivo === false) {
            //     dd('Error: No se pudo leer el contenido del archivo Gzip');
            // }

            // // Calcular el HASH (SHA256) del contenido del archivo
            // $hashArchivo = hash('sha256', $contenidoArchivo);

            $gestion = 2024;
            $periodo = 8;

            // dd(
            //     "url5 =< ".$url5,
            //     "header =< ".$header,
            //     "codigoAmbiente =< ".$codigoAmbiente,
            //     "codigoPuntoVenta =< ".$codigoPuntoVenta,
            //     "codigoSistema =< ".$codigoSistema,
            //     "codigoSucursal =< ".$codigoSucursal,
            //     "cufd =< ".$cufd,
            //     "cuis =< ".$cuis,
            //     "nit =< ".$nit,
            //     "contenidoArchivo =< ".$contenidoArchivo,
            //     "cantidadFacturas =< ".$cantidadFacturas,
            //     "fechaEmicion =< ".$fechaEmicion,
            //     "gestion =< ".$gestion,
            //     "hashArchivo =< ".$hashArchivo,
            //     "periodo =< ".$periodo
            // );

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
                
            dd($resultados, $consultaCompras);


            
            // //  ESTE ESTA BIEN
            // $rutaCarpeta = "assets/docs/paqueteCompras/envioPaqueteCompras";
            // // Verificar si la carpeta existe
            // if (!file_exists($rutaCarpeta))
            //     mkdir($rutaCarpeta, 0755, true);

            // // Obtener lista de archivos en la carpeta
            // $archivos = glob($rutaCarpeta . '/*');
            // // Eliminar cada archivo
            // foreach ($archivos as $archivo) {
            //     if (is_file($archivo))
            //         unlink($archivo);
            // }
            // $file = public_path('assets/docs/paqueteCompras/envioPaqueteCompras.tar.gz');
            // if (file_exists($file))
            //     unlink($file);

            // $file = public_path('assets/docs/paqueteCompras/envioPaqueteCompras.tar');
            // if (file_exists($file))
            //     unlink($file);

            // $idsToUpdate      = [];
            // $resultados       = [];
            // $cantidadFacturas = 0;

            // $filtered_array = array_filter($datos, function($value, $key) use (&$resultados, &$cantidadFacturas) {
            //     if (strpos($key, 'factura_') === 0 && $value === 'on') {
            //         // Realizar otras operaciones
            //         $numero = str_replace('factura_', '', $key);
            //         $resultados[] = (int) $numero;

            //         $factura = Factura::find($numero);
                    
            //         $xml                            = $factura->productos_xml;
            //         $archivoXML                     = new SimpleXMLElement($xml);

            //         // GUARDAMOS EN LA CARPETA EL XML
            //         $archivoXML->asXML("assets/docs/paqueteCompras/envioPaqueteCompras/facturaxmlCompras_$numero.xml");
            //         $cantidadFacturas++;

            //         // Retornar true para mantener este elemento en el array filtrado
            //         return true;
            //     }
            //     // Retornar false para excluir este elemento del array filtrado
            //     return false;
            // }, ARRAY_FILTER_USE_BOTH);

            // // Ruta de la carpeta que deseas comprimir
            // $rutaCarpeta = "assets/docs/paqueteCompras/envioPaqueteCompras";

            // // Nombre y ruta del archivo TAR resultante
            // $archivoTar = "assets/docs/paqueteCompras/envioPaqueteCompras.tar";

            // // Crear el archivo TAR utilizando la biblioteca PharData
            // $tar = new PharData($archivoTar);
            // $tar->buildFromDirectory($rutaCarpeta);

            // // Ruta y nombre del archivo comprimido en formato Gzip
            // $archivoGzip = "assets/docs/paqueteCompras/envioPaqueteCompras.tar.gz";

            // // ESTE ES OTRO CHEEE
            // // Abre el archivo .gz en modo de escritura
            // $gz = gzopen($archivoGzip, 'wb');
            // // Abre el archivo .tar en modo de lectura
            // $archivo = fopen($archivoTar, 'rb');
            // // Lee el contenido del archivo .tar y escribe en el archivo .gz
            // while (!feof($archivo)) {
            //     gzwrite($gz, fread($archivo, 8192));
            // }
            // // Cierra los archivos
            // fclose($archivo);
            // gzclose($gz);

            // // Leer el contenido del archivo comprimido
            // $contenidoArchivo = file_get_contents($archivoGzip);

            // // Calcular el HASH (SHA256) del contenido del archivo
            // $hashArchivo = hash('sha256', $contenidoArchivo);

            // $gestion = 2024;
            // $periodo = 8;

            // $consultaCompras = json_decode(
            //     $siat->recepcionPaqueteCompras(
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
                
            // dd($resultados, $filtered_array, $consultaCompras);


            

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
