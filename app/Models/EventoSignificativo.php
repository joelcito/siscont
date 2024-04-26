<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoSignificativo extends Model
{
    protected $table = 'eventos_significativos';
    use HasFactory, SoftDeletes;

    public function siat_evento(){
        return $this->belongsTo('App\Models\SiatEventoSignificativo', 'siat_evento_significativo_id');
    }

    public function cufd_activo(){
        return $this->belongsTo('App\Models\Cufd', 'cufd_activo_id');
    }

    public function cufd_antiguo(){
        return $this->belongsTo('App\Models\Cufd', 'cufd_activo_id');
    }

}
