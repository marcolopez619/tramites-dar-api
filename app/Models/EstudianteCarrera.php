<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteCarrera extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_carrera';
    protected $fillable = [ 'estado' ];
    protected $primaryKey = 'id_estudiante_carrera';
}
