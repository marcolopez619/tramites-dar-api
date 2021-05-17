<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'carrera';
    protected $fillable = [ 'nombre', 'estado' ];
    protected $primaryKey = 'id_carrera';

    public function facultad(){
        return $this->belongsTo( Facultad::class );
    }

    public function estudiante(){
        return $this->belongsToMany( Estudiante::class, 'estudiante_carrera', 'id_estudiante', 'id_carrera' );
    }

    public function usuarioPerfil(){
        return $this->hasMany( UsuarioPerfil::class, 'id_carrera' );
    }
}
