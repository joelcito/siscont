<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiatTipoDocumentoSector extends Model
{
    protected $table = 'siat_tipo_documento_sectores';
    use HasFactory, SoftDeletes;
}
