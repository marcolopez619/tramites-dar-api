<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspencion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'suspencion';
    protected $fillable = ['id_carrera', 'tiempo_solicitado', 'descripcion', 'fecha_solicitud', 'motivo' ];
    protected $primaryKey = 'id_suspencion';

    public function readmision(){
        return $this->hasOne( Readmision::class, 'id_suspencion' )
    }
}
