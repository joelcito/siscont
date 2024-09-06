<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    public function cliente(){
        return $this->belongsTo('App\Models\Cliente', 'cliente_id');
    }

    public function empresa(){
        return $this->belongsTo('App\Models\Empresa', 'empresa_id');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }

    public function punto_venta(){
        return $this->belongsTo('App\Models\PuntoVenta', 'punto_venta_id');
    }

    public function cufd(){
        return $this->belongsTo('App\Models\Cufd', 'cufd_id');
    }

    public function siat_tipo_documento_sector(){
        return $this->belongsTo('App\Models\SiatTipoDocumentoSector', 'siat_documento_sector_id');
    }


}
