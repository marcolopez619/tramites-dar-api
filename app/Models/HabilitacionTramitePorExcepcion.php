<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabilitacionTramitePorExcepcion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'habilitacion_tramite_por_excepcion';
    protected $fillable = ['fecha_inicial', 'fecha_final', 'id_estudiante', 'id_tramite', 'id_estado' ];
    protected $primaryKey = 'id_habilitacion_por_excepcion';
}
