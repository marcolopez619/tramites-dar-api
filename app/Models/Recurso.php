<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'recurso';
    protected $fillable = [ 'ruta', 'id_modulo' ];
    protected $primaryKey = 'id_recurso';

}
