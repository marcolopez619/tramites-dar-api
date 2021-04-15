<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteTramite extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_tramite';
    protected $fillable = ['fecha' , 'observaciones'];
    protected $primaryKey = 'id_estudiante_tramite';
}
