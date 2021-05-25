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
        return $this->belongsToMany( Anulacion::class, 'estudiante_tramite', 'id_estudiante', 'id_anulacion' );
    }

    public function cambioCarrera(){
        return $this->belongsToMany( CambioCarrera::class, 'estudiante_tramite', 'id_estudiante', 'id_cambio_carrera' );
    }

    public function transferencia(){
        return $this->belongsToMany( CambioCarrera::class, 'estudiante_tramite', 'id_estudiante', 'id_transferencia' );
    }

    public function suspencion(){
        return $this->belongsToMany( Suspencion::class, 'estudiante_tramite', 'id_estudiante', 'id_suspencion' );
    }

    public function readmision(){
        return $this->belongsToMany( Readmision::class, 'estudiante_tramite', 'id_estudiante', 'id_readmision' );
    }

    public function traspaso(){
        return $this->belongsToMany( Traspaso::class, 'estudiante_tramite', 'id_estudiante', 'id_traspaso' );
    }



    public function carrera(){
        return $this->belongsToMany( Carrera::class, 'estudiante_carrera', 'id_estudiante', 'id_carrera' );
    }

    public function tramite(){
        return $this->belongsToMany( Tramite::class, 'estudiante_tramite', 'id_estudiante', 'id_tramite');
    }


}
