<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'motivo';
    protected $fillable = ['descripcion' ];
    protected $primaryKey = 'id_motivo';
}
