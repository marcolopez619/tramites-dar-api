<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    use HasFactory;

    protected $fillable = [ 'nombre', 'estado' ];
    protected $timestamps = false;

    public function universidad()
    {
        return $this->belongsTo( Universidad::class );
    }

    public function carrera()
    {
        return $this->hasMany( Carrera::class );
    }

}
