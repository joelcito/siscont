<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiatProductoServicio extends Model
{
    use HasFactory, SoftDeletes;

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursal', 'sucursal_id');
    }   
    
    public function puntoVenta(){
        return $this->belongsTo('App\Models\PuntoVenta', 'punto_venta_id');
    }
}
