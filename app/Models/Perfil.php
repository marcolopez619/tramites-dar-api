<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;


    public $timestamps = false;
    protected $table = 'perfil';
    protected $fillable = ['nombre', 'id_usuario' ];
    protected $primaryKey = 'id_perfil';

    public function modulo(){
        return $this->belongsToMany( Modulo::class, 'perfil_modulo', 'id_perfil', 'id_modulo' );
    }

    public function usuario(){
        return $this->belongsToMany( usuario::class, 'usuario_perfil', 'id_usuario', 'id_perfil' );
    }
}
