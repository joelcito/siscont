<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrlApiServicioSiat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "url_apis_servicios_siat";

    public function siat_tipo_documento_sector(){
        return $this->belongsTo('App\Models\SiatTipoDocumentoSector', 'siat_documento_sector_id');
    }

    public function getUrlSincronizacion($ambiente, $modalidad){
        return $this->where('ambiente', $ambiente)
                    ->where('modalidad', $modalidad)
                    ->where('nombre', 'url_facturacionSincronizacion')
                    ->first();
    }

    public function getUrlCodigos($ambiente, $modalidad){
        return $this->where('ambiente', $ambiente)
                    ->where('modalidad', $modalidad)
                    ->where('nombre', 'url_facturacionCodigos')
                    ->first();
    }

    public function getUrlOperaciones($ambiente, $modalidad){
        return $this->where('ambiente', $ambiente)
                    ->where('modalidad', $modalidad)
                    ->where('nombre', 'url_facturacion_operaciones')
                    ->first();
    }

    public function getUrlFacturacionCompraVentaElctronica($ambiente, $modalidad){
        return $this->where('ambiente', $ambiente)
                    ->where('modalidad', $modalidad)
                    ->where('nombre', 'url_servicio_facturacion_compra_venta')
                    ->first();
    }

    public function getUrlFacturacionTasaCeroElectronica($ambiente, $modalidad){
        return $this->where('ambiente', $ambiente)
                    ->where('modalidad', $modalidad)
                    ->where('nombre', 'url_servicio_facturacion_compra_venta_tasa_cero_electronica')
                    ->first();
    }

}
