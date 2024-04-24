<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiatMotivoAnulacion extends Model
{
    protected $table = "siat_motivo_anulaciones";
    use HasFactory, SoftDeletes;
}
