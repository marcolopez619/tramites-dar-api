<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabilitacionTramitePorExcepcion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'habilitacion_tramite_por_excepcion';
    protected $fillable = ['fecha_habilitacion', 'fecha_regularizacion', 'motivo', 'id_estudiante', 'id_tramite', 'id_estado', 'id_periodo_gestion'];
    protected $primaryKey = 'id_habilitacion_por_excepcion';
}
