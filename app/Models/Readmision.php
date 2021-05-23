<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readmision extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'readmision';
    protected $fillable = ['id_carrera', 'fecha_solicitud', 'motivo', 'id_suspencion' ];
    protected $primaryKey = 'id_readmision';

    public function suspencion(){
        return $this->belongsTo( Suspencion::class, 'id_suspencion' );
    }

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_anulacion', 'id_readmision', 'id_estudiante');
    }
}
