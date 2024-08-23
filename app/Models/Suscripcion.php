<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suscripcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suscripciones';

    public function empresa(){
        return $this->belongsTo('App\Models\Empresa', 'empresa_id');
    }

    public function plan(){
        return $this->belongsTo('App\Models\Plan', 'plan_id');
    }

}
