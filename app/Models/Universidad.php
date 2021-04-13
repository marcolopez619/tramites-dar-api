<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    use HasFactory;


    public $timestamps = false;
    protected $table = 'universidad';
    protected $fillable = [ 'nombre', 'estado' ];
    protected $primaryKey = 'id_universidad';

    /**
     * Añade la relacion 1:N
     *
     * @return void
     */
    public function facultad()
    {
        return $this->hasMany( Facultad::class, 'id_universidad' );
    }
}
