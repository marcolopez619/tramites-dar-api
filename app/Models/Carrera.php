<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [ 'nombre', 'estado' ];
    protected $timestamps = false;

    public function facultad(){
        return $this->belongsTo( facultad::class );
    }
}
