<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpresaDocumentoSector extends Model
{
    protected $table = 'empresas_documentos_sectores';

    use HasFactory, SoftDeletes;

    public function siat_tipo_documento_sector(){
        return $this->belongsTo('App\Models\SiatTipoDocumentoSector', 'siat_documento_sector_id');
    }

    public function empresa(){
        return $this->belongsTo('App\Models\Empresa', 'empresa_id');
    }
}
