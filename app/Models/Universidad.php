<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    use HasFactory;

    protected $fillable = [ 'nombre', 'estado' ];
    protected $timestamps = false;

    /**
     * Añade la relacion 1:N
     *
     * @return void
     */
    public function facultad()
    {
        return $this->hasMany( Facultad::class );
    }
}
