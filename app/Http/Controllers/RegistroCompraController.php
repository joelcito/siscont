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

class RegistroCompraController extends Controller
{

    // Propiedades privadas
    private $_usuario;
    private $_empresa;
    private $_punto_venta;
    private $_sucursal;
    private $_cuis;

    // Constructor del controlador
    public function __construct()
    {
        // dd(Auth::user());

        // Aplica middleware a todas las rutas de este controlador
        $this->middleware('auth');

        $this->_usuario = Auth::user();

        dd($this->_usuario, Auth::user());

        if ($this->_usuario) {
            $this->_empresa     = Empresa::find($this->_usuario->empresa_id);
            $this->_punto_venta = PuntoVenta::find($this->_usuario->punto_venta_id);
            $this->_sucursal    = Sucursal::find($this->_usuario->sucursal_id);

            if ($this->_punto_venta && $this->_sucursal && $this->_empresa) {
                $this->_cuis = Cuis::where('punto_venta_id', $this->_punto_venta->id)
                                ->where('sucursal_id', $this->_sucursal->id)
                                ->where('codigo_ambiente', $this->_empresa->codigo_ambiente)
                                ->first();
            } else {
                // Manejo de caso cuando punto de venta, sucursal o empresa no se encuentran
                $this->_cuis = null;
            }
        } else {
            // Manejo del caso cuando el usuario no está autenticado
            $this->_empresa     = null;
            $this->_punto_venta = null;
            $this->_sucursal    = null;
            $this->_cuis        = null;
        }

    }

    /**
     * Display a listing of the resource.
     */
    public function listado(Request $request){
        dd(Auth::user());
        return view('registrocompra.listado');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajaxListado(Request $request){
        if($request->ajax()){

            // $usuario_id     = Auth::user()->id;
            // $empresa_id     = Auth::user()->empresa_id;
            // $sucursal_id    = Auth::user()->sucursal_id;
            // $punto_venta_id = Auth::user()->punto_venta_id;


            $usuario_id     = $this->_usuario->id;
            $empresa_id     = $this->_usuario->empresa_id;
            $sucursal_id    = $this->_usuario->sucursal_id;
            $punto_venta_id = $this->_usuario->punto_venta_id;

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

            // $usuario            = Auth::user();
            // $empresa_objeto     = Empresa::find($empresa_id);
            // $punto_venta_objeto = PuntoVenta::find($punto_venta_id);
            // $sucursal_objeto    = Sucursal::find($sucursal_id);

            $numeroFacturaEmpresa = $this->numeroFactura($this->_empresa->id, $this->_sucursal->id, $this->_punto_venta->id);
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

            // Guardar el contenido XML en un archivo
            $filePath = public_path('assets/docs/paqueteCompras/registroCompra.xml');
            file_put_contents($filePath, $xmlString);

            $siat = app(SiatController::class);

            $cufdVigente = json_decode(
                        $siat->verificarConeccion(
                            $this->_empresa->id,
                            $this->_sucursal->id,
                            $this->_cuis->id,
                            $this->_punto_venta->id,
                            $this->_empresa->codigo_ambiente
                        ));


            $factura                          = new Factura();
            $factura->usuario_creador_id      = $this->_usuario->id;
            $factura->empresa_id              = $this->_empresa->id;
            $factura->sucursal_id             = $this->_sucursal->id;
            $factura->punto_venta_id          = $this->_punto_venta->id;
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
    public function show(RegistroCompra $registroCompra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistroCompra $registroCompra)
    {
        //
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
