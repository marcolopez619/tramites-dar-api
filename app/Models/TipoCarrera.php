<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCarrera extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tipo_carrera';
    protected $fillable = ['descripcion'];
    protected $primaryKey = 'id_tipo_carrera';

    public function carrera(){
        return $this->hasMany( Carrera::class, 'id_tipo_carrera');
    }

    public function habilitacionTramite(){
        return $this->hasMany( HabilitacionTramite::class, 'id_tipo_carrera' );
    }
}
