<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilModulo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'perfil_modulo';
    protected $fillable = ['id_perfil', 'id_modulo' ];
    protected $primaryKey = 'id_perfil_moodulo';
}
