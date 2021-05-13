<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPerfil extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'usuario_perfil';
    protected $fillable = ['id_usuario', 'id_perfil' ];
    protected $primaryKey = 'id_usuario_perfil';
}
