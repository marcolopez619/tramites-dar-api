<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tramite';
    protected $fillable = ['descripcion'];
    protected $primaryKey = 'id_tramite';

    public function habilitacionTramite(){
        return $this->hasMany( HabilitacionTramite::class, 'id_tramite' );
    }

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class , 'estudiante_tramite' , 'id_estudiante', 'id_tramite' );
    }

    public function habilitacionTramitePorExcepcion(){
        return $this->hasMany( HabilitacionTramitePorExcepcion::class, 'id_tramite');
    }
}
