<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'estado';
    protected $fillable = ['descripcion' ];
    protected $primaryKey = 'id_estado';

    public function estudianteTramite(){
        return $this->hasMany( EstudianteTramite::class , 'id_estado' );
    }

    public function habilitacionTramitePorExcepcion(){
        return $this->hasMany( HabilitacionTramitePorExcepcion::class, 'id_estado');
    }
}
