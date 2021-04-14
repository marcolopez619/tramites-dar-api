<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'traspaso';
    protected $fillable = ['id_univ_destino', 'id_carrera_destino', 'descripcion', 'anio_ingreso', 'materias_aprobadas', 'materias_reprobadas', 'fecha_solicitud', 'motivo' ];
    protected $primaryKey = 'id_traspaso';
}
