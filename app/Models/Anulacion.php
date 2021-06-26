<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anulacion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'anulacion';
    protected $fillable = ['fecha_solicitud', 'id_carrera_origen', 'id_motivo' ];
    protected $primaryKey = 'id_anulacion';

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_tramite',  'id_anulacion', 'id_estudiante' );
    }
}
