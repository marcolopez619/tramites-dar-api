<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante';
    protected $fillable = ['ru', 'ci', 'complemento','paterno', 'materno', 'nombres', 'fecha_nacimiento', 'sexo' ];
    protected $primaryKey = 'id_estudiante';

    public function anulacion(){
        return $this->belongsToMany( Anulacion::class, 'estudiante_anulacion', 'id_estudiante', 'id_anulacion' );
    }

    public function cambioCarrera(){
        return $this->hasMany( CambioCarrera::class, 'id_estudiante' );
    }

    public function suspencion(){
        return $this->hasMany( Suspencion::class, 'id_estudiante' );
    }

    public function readmision(){
        return $this->hasMany( Readmision::class, 'id_estudiante' );
    }

    public function traspaso(){
        return $this->hasMany( Traspaso::class, 'id_estudiante' );
    }

    public function transferencia(){
        return $this->hasMany( transferencia::class, 'id_estudiante' );
    }

    public function carrera(){
        return $this->belongsToMany( Carrera::class, 'estudiante_carrera', 'id_estudiante', 'id_carrera' );
    }

    public function tramite(){
        return $this->belongsToMany( Tramite::class, 'estudiante_tramite', 'id_estudiante', 'id_tramite');
    }


}
