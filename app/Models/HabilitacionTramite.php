<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabilitacionTramite extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'habilitacion_tramite';
    protected $fillable = ['fecha_inicial', 'fecha_final', 'estado', 'gestion' ];
    protected $primaryKey = 'id_hab_tramite';
}
