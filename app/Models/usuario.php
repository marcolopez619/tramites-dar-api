<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'usuario';
    protected $fillable = ['nombre', 'password', 'celular', 'estado', 'id_universidad'];
    protected $primaryKey = 'id_usuario';

    public function perfil(){
        return $this->belongsToMany( Perfil::class, 'usuario_perfil', 'id_usuario', 'id_perfil' );
    }
}
