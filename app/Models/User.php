<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function rol(){
        return $this->belongsTo('App\Models\Rol', 'rol_id');
    }

    public function empresa(){
        return $this->belongsTo('App\Models\Empresa', 'empresa_id');
    }

    public function  isAdmin(){
        return $this->rol_id == 1 ? true : false;
    }

    public function  isCajero(){
        return $this->rol_id == 3 ? true : false;
    }

    public function  isJefeEmpresa(){
        return $this->rol_id == 2 ? true : false;
    }

    public function isFacturacionCompraVenta(){
        $empresa = $this->empresa;
        return $empresa->codigo_documento_sector == '1' ? true : false;
    }

    public function isFacturacionTasaCero(){
        $empresa = $this->empresa;
        return $empresa->codigo_documento_sector == '8' ? true : false;
    }

}
