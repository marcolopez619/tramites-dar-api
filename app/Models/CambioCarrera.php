<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CambioCarrera extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cambio_carrera';
    protected $fillable = ['id_carrera_origen', 'id_carrera_destino', 'fecha_solicitud', 'convalidacion', 'motivo' ];
    protected $primaryKey = 'id_cambio_carrera';

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_anulacion',  'id_cambio_carrera', 'id_estudiante' );
    }
}
