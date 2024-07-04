<?php

namespace App\Http\Controllers;

use App\Models\Cufd;
use App\Models\Cuis;
use App\Models\Empresa;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapFault;

class SiatController extends Controller
{

    protected $header ;
    protected $timeout = 1;
    // protected $timeout = 5;
    // protected $timeout = 20;
    protected $codigoAmbiente ;
    protected $codigoModalidad ;
    protected $codigoPuntoVenta ;
    protected $codigoSistema ;
    protected $codigoSucursal ;
    protected $nit ;
    protected $codigoDocumentoSector ;
    protected $url1 ;
    protected $url2 ;
    protected $url3 ;
    protected $url4 ;

    // public function __construct($header, $timeout, $codigoAmbiente, $codigoModalidad, $codigoPuntoVenta, $codigoSistema, $codigoSucursal, $nit, $codigoDocumentoSector, $url1, $url2, $url3, $url4){
    // public function __construct($header, $codigoAmbiente, $codigoModalidad, $codigoPuntoVenta, $codigoSistema, $codigoSucursal, $nit, $codigoDocumentoSector, $url1, $url2, $url3, $url4){
    //     $this->header                = $header;
    //     $this->timeout               = 5;
    //     $this->codigoAmbiente        = $codigoAmbiente;
    //     $this->codigoModalidad       = $codigoModalidad;
    //     $this->codigoPuntoVenta      = $codigoPuntoVenta;
    //     $this->codigoSistema         = $codigoSistema;
    //     $this->codigoSucursal        = $codigoSucursal;
    //     $this->nit                   = $nit;
    //     $this->codigoDocumentoSector = $codigoDocumentoSector;
    //     $this->url1                  = $url1;
    //     $this->url2                  = $url2;
    //     $this->url3                  = $url3;
    //     $this->url4                  = $url4;
    // }

    // public function __construct(){
    //     $this->header                = "";
    //     $this->timeout               = "";
    //     $this->codigoAmbiente        = "";
    //     $this->codigoModalidad       = "";
    //     $this->codigoPuntoVenta      = "";
    //     $this->codigoSistema         = "";
    //     $this->codigoSucursal        = "";
    //     $this->nit                   = "";
    //     $this->codigoDocumentoSector = "";
    //     $this->url1                  = "";
    //     $this->url2                  = "";
    //     $this->url3                  = "";
    //     $this->url4                  = "";
    // }

    // protected $header                   = "apikey: TokenApi eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI1NDI3NjQ4U2N6IiwiY29kaWdvU2lzdGVtYSI6Ijc3MkNCMUI1QTc0OUI0MTk0MjBGQjA2Iiwibml0IjoiSDRzSUFBQUFBQUFBQURNMU1USTNNN0V3TURRREFBc2lNQ29LQUFBQSIsImlkIjoxMDE3OTY5LCJleHAiOjE3MzU2ODkzMTUsImlhdCI6MTcwNjY3MzI4NSwibml0RGVsZWdhZG8iOjU0Mjc2NDgwMTYsInN1YnNpc3RlbWEiOiJTRkUifQ.DyCmanTysmzWWQ3TYV2X90oQf7C0fq36Ys3DCWyjmuM2hHxbeuLUfWWlTmewS59t37QnO4l9qiv1ZTdMVZjfAA";
    // protected $timeout                  = 5;                            // TIEMPO EN ESPERA PARA QUE RESPONDA SITA
    // protected $codigoAmbiente           = 2;                            // si estamos desarrollo o pruebas  1 Produccion --- 2 Desarrollo
    // protected $codigoModalidad          = 1;                            // que modalidad de facturacion es  1 Electronica --- 2 Computarizada
    // protected $codigoPuntoVenta;                                        // NUMOER DE QUE PUNTO DE VENTA ES
    // protected $codigoSistema            = "772CB1B5A749B419420FB06";    // CODIGO DE SISTEMA QUE TE DA SIAT
    // protected $codigoSucursal           = 0;                            // CODIGO DE TU SUCURSAL
    // protected $nit                      = "5427648016";                 // NIT DE LA EMPRESA
    // protected $codigoDocumentoSector    = 1;                            // COMPRA Y VENTA
    // protected $url1                     = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl";
    // protected $url2                     = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
    // protected $url3                     = "https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionCompraVenta?wsdl";
    // protected $url4                     = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";


    // ********************* GENERACION DE DATOS *********************
    public function cuis($header,$url1,$codigoAmbiente,$codigoModalidad,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$nit){
        // $wsdl               = $this->url1;
        // $codigoAmbiente     = $this->codigoAmbiente;
        // $codigoModalidad    = $this->codigoModalidad;
        // $codigoPuntoVenta   = $this->codigoPuntoVenta; //
        // $codigoSistema      = $this->codigoSistema;
        // $codigoSucursal     = $this->codigoSucursal;
        // $nit                = $this->nit;

        // dd($url1,$codigoAmbiente,$codigoModalidad,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$nit);

        $wsdl               = $url1;
        // $codigoAmbiente     = $codigoAmbiente;
        // $codigoModalidad    = $codigoModalidad;
        // $codigoPuntoVenta   = $codigoPuntoVenta;
        // $codigoSistema      = $codigoSistema;
        // $codigoSucursal     = $codigoSucursal;
        // $nit                = $nit;

        $parametros         =  array(
            'SolicitudCuis' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoModalidad'   => $codigoModalidad,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header'  => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl'     => WSDL_CACHE_NONE,
                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->cuis($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
            $data['msg']       = $fault;

        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function registroPuntoVenta($descripcionPuntoVenta, $nombrePuntoVenta, $header, $url4, $codigoAmbiente, $codigoModalidad, $codigoSistema, $codigoSucursal, $codigoTipoPuntoVenta, $scuis, $nit ){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url4;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoModalidad        = $codigoModalidad;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $codigoTipoPuntoVenta   = $codigoTipoPuntoVenta;                        //PUNTO VENTA VENTANILLA DE COBRANZA
        $cuis                   = $scuis;
        $descripcion            = $descripcionPuntoVenta;
        $nit                    = $nit;
        $nombrePuntoVenta       = $nombrePuntoVenta;

        $parametros         =  array(
            'SolicitudRegistroPuntoVenta' => array(
                'codigoAmbiente'        => $codigoAmbiente,
                'codigoModalidad'       => $codigoModalidad,
                'codigoSistema'         => $codigoSistema,
                'codigoSucursal'        => $codigoSucursal,
                'codigoTipoPuntoVenta'  => $codigoTipoPuntoVenta,
                'cuis'                  => $cuis,
                'descripcion'           => $descripcion,
                'nit'                   => $nit,
                'nombrePuntoVenta'      => $nombrePuntoVenta
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->registroPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }






    // ********************* SINCRONIZACION DE CATALOGOS *********************
    public function sincronizarParametricaTipoDocumentoSector($header, $url2, $codigoAmbiente, $codigoPuntoVenta ,$codigoSistema ,$codigoSucursal ,$scuis ,$nit ){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $wsdl                   = $this->url2;
        // $codigoAmbiente         = $this->codigoAmbiente;
        // $codigoPuntoVenta       = $this->codigoPuntoVenta;
        // $codigoSistema          = $this->codigoSistema;
        // $codigoSucursal         = $this->codigoSucursal;
        // $cuis                   = session('scuis');
        // $nit                    = $this->nit;

        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoDocumentoSector($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
            $data['msg']       = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoPuntoVenta($header, $url2, $codigoAmbiente, $codigoPuntoVenta, $codigoSistema, $codigoSucursal, $scuis, $nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $wsdl                   = $this->url2;
        // $codigoAmbiente         = $this->codigoAmbiente;
        // $codigoPuntoVenta       = $this->codigoPuntoVenta;
        // $codigoSistema          = $this->codigoSistema;
        // $codigoSucursal         = $this->codigoSucursal;
        // $cuis                   = session('scuis');
        // $nit                    = $this->nit;

        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarActividades($header,$url2,$codigoAmbiente,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$scuis,$nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $wsdl               = $this->url2;
        // $codigoAmbiente     = $this->codigoAmbiente;
        // $codigoPuntoVenta   = $this->codigoPuntoVenta;
        // $codigoSistema      = $this->codigoSistema;
        // $codigoSucursal     = $this->codigoSucursal;
        // $cuis               = session('scuis');
        // $nit                = $this->nit;

        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarActividades($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaProductosServicios($header,$url2,$codigoAmbiente,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$scuis,$nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaProductosServicios($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
            $data['msg']       = $fault;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaUnidadMedida( $header, $url2, $codigoAmbiente, $codigoPuntoVenta, $codigoSistema, $codigoSucursal, $scuis, $nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaUnidadMedida($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoDocumentoIdentidad($header,$url2,$codigoAmbiente,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$scuis,$nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoDocumentoIdentidad($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoMetodoPago($header,$url2,$codigoAmbiente,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$scuis,$nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoMetodoPago($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoMoneda($header,$url2,$codigoAmbiente,$codigoPuntoVenta,$codigoSistema,$codigoSucursal,$scuis,$nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoMoneda($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaMotivoAnulacion(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaMotivoAnulacion($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaEventosSignificativos(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaEventosSignificativos($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarFechaHora(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarFechaHora($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaActividadesDocumentoSector(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaActividadesDocumentoSector($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaLeyendasFactura(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaLeyendasFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaMensajesServicios(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl               = $url2;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoPuntoVenta   = $codigoPuntoVenta;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaMensajesServicios($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaPaisOrigen(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;


        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaPaisOrigen($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoEmision(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoEmision($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoHabitacion(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoHabitacion($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTiposFactura(
        $header,
        $url2,
        $codigoAmbiente,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        // $this->verificarConeccion();
        $wsdl                   = $url2;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cuis                   = $scuis;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTiposFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // ********************* END SINCRONIZACION DE CATALOGOS *********************

    public function anulacionFactura(
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

        $codMot, $cuf1
        ){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!

        $wsdl                   = $url3;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoDocumentoSector  = $codigoDocumentoSector; //NUEVO SECTOR EDUCATIIVO
        $codigoEmision          = 1; //NUEVO LINENA
        $codigoModalidad        = $codigoModalidad;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd; //NUEVO
        $cuis                   = $scuis;
        $nit                    = $nit;
        // $tipoFacturaDocumento   = 1; //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $codigoMotivo           = $codMot;
        $cuf                    = $cuf1;

        $parametros         =  array(
            'SolicitudServicioAnulacionFactura' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'codigoMotivo'              => $codigoMotivo,
                'cuf'                       => $cuf,
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->anulacionFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function reversionAnulacionFactura(
        $header,
        $url3,
        $codigoAmbiente,
        $codigoDocumentoSector ,
        $codigoModalidad,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scufd,
        $scuis,
        $nit,
        $cuf,
        $tipoFacturaDocumento
    ){

        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!

        $wsdl                   = $url3;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoDocumentoSector  = $codigoDocumentoSector; //NUEVO SECTOR EDUCATIIVO
        $codigoEmision          = 1; //NUEVO LINENA
        $codigoModalidad        = $codigoModalidad;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd; //NUEVO
        $cuis                   = $scuis;
        $nit                    = $nit;
        // $tipoFacturaDocumento   = 1; //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $cuf                    = $cuf;

        $parametros         =  array(
            'SolicitudServicioReversionAnulacionFactura' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'cuf'                       => $cuf,
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl'     => WSDL_CACHE_NONE,
                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->reversionAnulacionFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);

    }



    public function consultaPuntoVenta($header,$url4, $codigoAmbiente, $codigoSistema, $codigoSucursal, $scuis, $nit){
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl               = $url4;
        $codigoAmbiente     = $codigoAmbiente;
        $codigoSistema      = $codigoSistema;
        $codigoSucursal     = $codigoSucursal;
        $cuis               = $scuis;
        $nit                = $nit;

        $parametros         =  array(
            'SolicitudConsultaPuntoVenta' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->consultaPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
            $data['msg']       = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function verificarComunicacion($url1,$header){
        $wsdl = $url1;
        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);
        $data    = array();

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl'     => WSDL_CACHE_NONE,
                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);
            $resultado         = $client->verificarComunicacion();
            $data['estado']    = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado         = false;
            $data['estado']    = 'error';
            $data['resultado'] = $resultado;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function cufd(
        $header,
        $url1,
        $codigoAmbiente,
        $codigoModalidad,
        $codigoPuntoVenta,
        $codigoSistema,
        $codigoSucursal,
        $scuis,
        $nit
    ){
        $comunicacion =  json_decode($this->verificarComunicacion($url1,$header));
        if($comunicacion->estado == "success"){
            if($comunicacion->resultado->RespuestaComunicacion->transaccion){
                $wsdl               = $url1;
                $codigoAmbiente     = $codigoAmbiente;
                $codigoModalidad    = $codigoModalidad;
                $codigoPuntoVenta   = $codigoPuntoVenta;
                $codigoSistema      = $codigoSistema;
                $codigoSucursal     = $codigoSucursal;
                $cuis               = $scuis;
                $nit                = $nit;
                $parametros         =  array(
                    'SolicitudCufd' => array(
                        'codigoAmbiente'    => $codigoAmbiente,
                        'codigoModalidad'   => $codigoModalidad,
                        'codigoPuntoVenta'  => $codigoPuntoVenta,
                        'codigoSistema'     => $codigoSistema,
                        'codigoSucursal'    => $codigoSucursal,
                        'cuis'              => $cuis,
                        'nit'               => $nit
                    )
                );
                $aoptions = array(
                    'http' => array(
                        'header' => $header,
                        'timeout' => $this->timeout
                    ),
                );
                $context = stream_context_create($aoptions);
                try {
                    $client = new \SoapClient($wsdl,[
                        'stream_context' => $context,
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
                    ]);

                    $resultado = $client->cufd($parametros);
                    $data['estado']     = 'success';
                    $data['resultado']  = $resultado;
                } catch (SoapFault $fault) {
                    $resultado           = false;
                    $data['estado']      = 'error';
                    $data['resultado']   = $resultado;
                    $data['error']       = $fault;
                    $data['msgS']        = $fault->getMessage();
                }   catch (Exception $e) {
                    // Captura cualquier otra excepción y maneja el error
                    $data['msgE']        = $e->getMessage();
                }
                return json_encode($data, JSON_UNESCAPED_UNICODE);

            }else{
                $data['estado']     = 'error';
                $data['resultado']  = $comunicacion->resultado;
            }
        }else{
            $data['estado']    = 'error';
            $data['resultado'] = $comunicacion;
        }
        return $data;
    }



    public function recepcionFactura(
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


        $arch, $fecEnv, $hasArch
        ){

            // dd(
            //     $header,
            //     $url3,
            //     $codigoAmbiente,
            //     $codigoDocumentoSector,
            //     $codigoModalidad,
            //     $codigoPuntoVenta,
            //     $codigoSistema,
            //     $codigoSucursal,
            //     $scufd,
            //     $scuis,
            //     $nit,


            //     $arch, $fecEnv, $hasArch
            // );
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url3;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoDocumentoSector  = $codigoDocumentoSector;     //NUEVO SECTOR EDUCATIIVO
        $codigoEmision          = 1;                                //NUEVO LINENA
        $codigoModalidad        = $codigoModalidad;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd; //NUEVO
        $cuis                   = $scuis;
        $nit                    = $nit;
        $tipoFacturaDocumento   = $tipoFacturaDocumento;                        //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $archivo                = $arch;
        $fechaEnvio             = $fecEnv;
        $hashArchivo            = $hasArch;

        $parametros         =  array(
            'SolicitudServicioRecepcionFactura' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'archivo'                   => $archivo,
                'fechaEnvio'                => $fechaEnvio,
                'hashArchivo'               => $hashArchivo
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->recepcionFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }



    public function verificarConeccion($empresa_id, $sucursal_id, $cuis_id, $punto_venta_id, $codigoAmbiente ){

        $cufdDelDia = Cufd::where('empresa_id', $empresa_id)
                            ->where('sucursal_id', $sucursal_id)
                            ->where('cuis_id', $cuis_id)
                            ->where('punto_venta_id', $punto_venta_id)
                            ->where('codigo_ambiente', $codigoAmbiente)
                            ->latest()
                            ->first();

        // dd($cufdDelDia);

        $cufdRescatadoUtilizar = null;

        $empresa     = Empresa::find($empresa_id);
        $sucursal    = Sucursal::find($sucursal_id);
        $cuis        = Cuis::find($cuis_id);
        $punto_venta = PuntoVenta::find($punto_venta_id);


        $header           = $empresa->api_token;
        // dd($header);
        $url1             = $empresa->url_facturacionCodigos;
        $codigoAmbiente   = $empresa->codigo_ambiente;
        $codigoModalidad  = $empresa->codigo_modalidad;
        $codigoPuntoVenta = $punto_venta->codigoPuntoVenta;
        $codigoSistema    = $empresa->codigo_sistema;
        $codigoSucursal   = $sucursal->codigo_sucursal;
        $scuis            = $cuis->codigo;
        $nit              = $empresa->nit;

        if($cufdDelDia){
            $fechaVigencia = $cufdDelDia->fecha_vigencia;
            // dd($fechaVigencia < date('Y-m-d H:i'));
            if($fechaVigencia < date('Y-m-d H:i')){

                $cufd = json_decode($this->cufd(
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

                if($cufd->estado == "success"){
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
                    }else{
                    }
                }else{
                }
                // dd('$fechaVigencia < date("Y-m-d H:i")', "NO", $fechaVigencia, date("Y-m-d H:i"), $fechaVigencia < date("Y-m-d H:i"));
            }else{
                $cufdRescatadoUtilizar = $cufdDelDia;
            }
        }else{
            $cufd = json_decode($this->cufd(
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
            if($cufd->estado == "success"){
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

                }else{

                }
            }else{

            }
        }
        return $cufdRescatadoUtilizar;
    }

    public function registroEventoSignificativo(
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
        ){

        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!

        $wsdl                   = $url4;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoMotivoEvento     = $codMotEvent;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd;
        $cufdEvento             = $cufdEvent;
        $cuis                   = $scuis;
        $descripcion            = $desc;
        $fechaHoraFinEvento     = $fechaFin;
        $fechaHoraInicioEvento  = $fechaIni;
        $nit                    = $nit;

        $parametros         =  array(
            'SolicitudEventoSignificativo' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoMotivoEvento'        => $codigoMotivoEvento,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cufdEvento'                => $cufdEvento,
                'cuis'                      => $cuis,
                'descripcion'               => $descripcion,
                'fechaHoraFinEvento'        => $fechaHoraFinEvento,
                'fechaHoraInicioEvento'     => $fechaHoraInicioEvento,
                'nit'                       => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->registroEventoSignificativo($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function recepcionPaqueteFactura(
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

        $arch, $fechaenv,$hasarch, $cafcC, $canFact, $codEvent
        ){
        // $this->verificarConeccion();

        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!

        $wsdl                   = $url3;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoDocumentoSector  = $codigoDocumentoSector;     // SECTOR EDUCATIVO
        // $codigoEmision          = 2;                                // FUERA DE  LINEA (LINEA = 1 | FUERA DE LINEA = 2)
        $codigoEmision          = $tipo_online_o_offline;                                // FUERA DE  LINEA (LINEA = 1 | FUERA DE LINEA = 2)
        $codigoModalidad        = $codigoModalidad;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd;
        $cuis                   = $scuis;
        $nit                    = $nit;
        // $tipoFacturaDocumento   = 1;                        //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $archivo                = $arch;
        $fechaEnvio             = $fechaenv;
        $hashArchivo            = $hasarch;
        $cafc                   = $cafcC;
        $cantidadFacturas       = $canFact;
        $codigoEvento           = $codEvent;

        $parametros         =  array(
            'SolicitudServicioRecepcionPaquete' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'archivo'                   => $archivo,
                'fechaEnvio'                => $fechaEnvio,
                'hashArchivo'               => $hashArchivo,
                'cafc'                      => $cafc,
                'cantidadFacturas'          => $cantidadFacturas,
                'codigoEvento'              => $codigoEvento
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->recepcionPaqueteFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function validacionRecepcionPaqueteFactura(
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

        $codEmision, $codRecepcion
        ){
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        // $this->verificarConeccion();
        // ESO VERIFICAR !!!!!!!!!!!!! OJOOOO !!!!!!!!!!! PIOJO!!!!!!!!!
        $wsdl                   = $url3;
        $codigoAmbiente         = $codigoAmbiente;
        $codigoDocumentoSector  = $codigoDocumentoSector;                           //SECTOR EDUCATIVO
        $codigoEmision          = $codEmision;                  //NUEVO LINENA 1 LINEA | 2 FUENRA DE LINEA
        $codigoModalidad        = $codigoModalidad;
        $codigoPuntoVenta       = $codigoPuntoVenta;
        $codigoSistema          = $codigoSistema;
        $codigoSucursal         = $codigoSucursal;
        $cufd                   = $scufd;
        $cuis                   = $scuis;
        $nit                    = $nit;
        // $tipoFacturaDocumento   = 1;                            //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $codigoRecepcion        = $codRecepcion;

        $parametros         =  array(
            'SolicitudServicioValidacionRecepcionPaquete' => array(
                'codigoAmbiente'          => $codigoAmbiente,
                'codigoDocumentoSector'   => $codigoDocumentoSector,
                'codigoEmision'           => $codigoEmision,
                'codigoModalidad'         => $codigoModalidad,
                'codigoPuntoVenta'        => $codigoPuntoVenta,
                'codigoSistema'           => $codigoSistema,
                'codigoSucursal'          => $codigoSucursal,
                'cufd'                    => $cufd,
                'cuis'                    => $cuis,
                'nit'                     => $nit,
                'tipoFacturaDocumento'    => $tipoFacturaDocumento,
                'codigoRecepcion'         => $codigoRecepcion,
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->validacionRecepcionPaqueteFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}
