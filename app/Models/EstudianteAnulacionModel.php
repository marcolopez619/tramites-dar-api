<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteAnulacionModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_anulacion';
    protected $fillable = ['id_tramite', 'id_estado', 'id_entidad','fecha_proceso', 'observaciones', 'id_estudiante', 'id_anulacion' ];
    protected $primaryKey = 'id_estudiante_anulacion';

}
