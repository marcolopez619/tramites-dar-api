<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readmision extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estudiante_tramite';
    protected $fillable = ['id_estudiante', 'id_tramite', 'id_estado', 'id_entidad', 'fecha', 'observaciones' ];
    protected $primaryKey = 'id_estudiante_tramite';

    public function suspencion(){
        return $this->belongsTo( Suspencion::class );
    }
}
