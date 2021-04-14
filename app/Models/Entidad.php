<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'entidad';
    protected $fillable = ['descripcion' ];
    protected $primaryKey = 'id_entidad';

    public function estudianteTramite(){
        return $this->hasMany( EstudianteTramite::class , 'id_entidad' );
    }
}
