<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'facultad';
    protected $fillable = [ 'nombre', 'estado' ];
    protected $primaryKey = 'id_facultad';

    public function carrera()
    {
        return $this->hasMany( Carrera::class, 'id_facultad' );
    }

}
