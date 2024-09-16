<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';


    public function facturas(){
        return $this->hasMany(Factura::class);
    }

    public function cantidadFacturas(){
        return $this->facturas()->count();
    }

}
