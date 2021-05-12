<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'modulo';
    protected $fillable = [ 'nombre' ];
    protected $primaryKey = 'id_modulo';

    public function recurso(){
        return $this->hasMany( recurso::class, 'id_modulo' );
    }

    public function perfil(){
        return $this->belongsToMany( Perfil::class, 'perfil_modulo', 'id_perfil', 'id_modulo' );
    }
}
