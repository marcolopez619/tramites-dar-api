<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteTramite extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_tramite';
    protected $fillable = ['fecha' , 'observaciones', 'id_tipo_tramite'];
    protected $primaryKey = 'id_estudiante_tramite';
}
