<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoapFault;

class SiatController extends Controller
{

    protected $header ;
    protected $timeout = 5;
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

}
