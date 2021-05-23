<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspencion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'suspencion';
    protected $fillable = ['id_carrera', 'tiempo_solicitado', 'descripcion', 'fecha_solicitud', 'id_motivo' ];
    protected $primaryKey = 'id_suspencion';

    public function readmision(){
        return $this->hasOne( Readmision::class, 'id_suspencion' );
    }

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_tramite', 'id_suspencion', 'id_estudiante');
    }
}
