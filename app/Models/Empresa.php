<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    public function cuisVigente($sucursal_id, $punto_venta_id, $codigoAmbiente){

        return Cuis::where('punto_venta_id', $punto_venta_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->where('codigo_ambiente', $codigoAmbiente)
                    ->orderBy('id', 'desc')
                    ->first();
                    // ->toSql();

    }

    public function empresasDocumentos(){
        return $this->hasMany('App\Models\EmpresaDocumentoSector', 'empresa_id');
    }

    public function empresasDocumentosTipoSector($documento_sector){
        return $this->hasMany('App\Models\EmpresaDocumentoSector', 'empresa_id')
                    ->join('siat_tipo_documento_sectores', 'siat_tipo_documento_sectores.id', '=', 'empresas_documentos_sectores.siat_documento_sector_id')
                    ->where('siat_tipo_documento_sectores.codigo_clasificador', $documento_sector)
                    ->first();
    }

    public function sucursales(){
        return $this->hasMany(Sucursal::class, 'empresa_id', 'id');
    }

    public function clientes(){
        return $this->hasMany(Cliente::class, 'empresa_id', 'id');
    }

    public function usuarios(){
        return $this->hasMany(User::class, 'empresa_id', 'id');
    }

    public function facturas(){
        return $this->hasMany(Factura::class, 'empresa_id', 'id');
    }

    public function suscripciones(){
        return $this->hasMany(Suscripcion::class, 'empresa_id', 'id');
    }

    public function empresas_documentos_sectores(){
        return $this->hasMany(EmpresaDocumentoSector::class, 'empresa_id', 'id');
    }

    public function servicios(){
        return $this->hasMany(Servicio::class, 'empresa_id', 'id');
    }

}
