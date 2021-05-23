<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'traspaso';
    protected $fillable = ['id_univ_destino', 'id_carrera_destino', 'descripcion', 'anio_ingreso', 'materias_aprobadas', 'materias_reprobadas', 'fecha_solicitud', 'id_motivo', 'id_carrera_origen' ];
    protected $primaryKey = 'id_traspaso';

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_tramite', 'id_traspaso', 'id_estudiante');
    }
}
