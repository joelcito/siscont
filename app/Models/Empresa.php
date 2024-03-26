<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    public function cuisVigente($sucursal_id, $punto_venta_id, $codigoAmbiente) : Cuis {

        return Cuis::where('punto_venta_id', $punto_venta_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->where('codigo_ambiente', $codigoAmbiente)
                    ->first();
        
    }
}
