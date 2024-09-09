<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    // Relación: un cliente tiene muchas facturas
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    // Método para contar facturas
    public function contarFacturas()
    {
        return $this->facturas()->count();
    }

}
