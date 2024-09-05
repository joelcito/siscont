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

}
