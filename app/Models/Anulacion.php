<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anulacion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'anulacion';
    protected $fillable = ['fecha_solicitud', 'motivo', 'id_carrera_origen' ];
    protected $primaryKey = 'id_anulacion';

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_anulacion',  'id_anulacion', 'id_estudiante' );
    }
}
