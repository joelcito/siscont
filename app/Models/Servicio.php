<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    public function siatDependeActividad(){
        return $this->belongsTo('App\Models\SiatDependeActividades', 'siat_depende_actividades_id');
    }

    public function siatProductoServicio(){
        return $this->belongsTo('App\Models\SiatProductoServicio', 'siat_producto_servicios_id');
    }

    public function siatUnidadMedida(){
        return $this->belongsTo('App\Models\SiatUnidadMedida', 'siat_unidad_medidas_id');
    }
}
