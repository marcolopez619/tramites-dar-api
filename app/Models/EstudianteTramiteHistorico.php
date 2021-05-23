<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteTramiteHistorico extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_tramite_historico';
    protected $fillable = ['id_tramite', 'id_estado', 'id_entidad','fecha_proceso', 'observaciones', 'id_estudiante',
                           'id_anulacion', 'id_cambio_carrera', 'id_transferencia', 'id_suspencion', 'id_readmision', 'id_traspaso' ];
    protected $primaryKey = 'id_estudiante_tramite_historico';
}
