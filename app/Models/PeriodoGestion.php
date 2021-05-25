<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoGestion extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'periodo_gestion';
    protected $fillable = [ 'id_periodo', 'id_gestion', 'estado'];
    protected $primaryKey = 'id_periodo_gestion';

    public function habilitacionTramite(){
        return $this->hasMany( HabilitacionTramite::class, 'id_periodo_gestion' );
    }
}
