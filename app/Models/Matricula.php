<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'matricula';
    protected $fillable = [ 'id_periodo_gestion', 'id_estudiante', 'estado' ];
    protected $primaryKey = 'id_matricula';
}
