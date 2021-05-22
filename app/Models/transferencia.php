<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transferencia extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'transferencia';
    protected $fillable = ['id_carrera_origen', 'id_carrera_destino', 'fecha_solicitud', 'motivo', 'convalidacion' ];
    protected $primaryKey = 'id_transferencia';

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_anulacion',  'id_transferencia', 'id_estudiante' );
    }
}
