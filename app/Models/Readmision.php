<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readmision extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'readmision';
    protected $fillable = ['id_carrera', 'fecha_solicitud', 'motivo' ];
    protected $primaryKey = 'id_readmision';

    public function suspencion(){
        return $this->belongsTo( Suspencion::class, 'id_suspencion' );
    }
}
