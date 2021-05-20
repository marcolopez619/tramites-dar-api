<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectorModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DirectorController extends Controller
{
    public function getTramitesPorAtender($idCarrera){
        $arrayCamposSelect = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo',

            'anulacion.id_anulacion AS idTipoTramite',
            'anulacion.fecha_solicitud AS fechaSolicitud',
            'anulacion.id_carrera_origen AS idCarreraOrigen',
            'carrera.nombre AS carrera',
            'anulacion.motivo',

            'estudiante_anulacion.id_estudiante_anulacion as idEstudianteTipoTramiteTablaIntermedia',
            'estudiante_anulacion.fecha_proceso AS fechaProceso',
            'estudiante_anulacion.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $estudiante = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_anulacion.id_anulacion')

            ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_anulacion.id_estado', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where( 'estudiante_anulacion.id_entidad', '=' , 3 ) // FIXME: DATOS QUEMADO
            ->where( 'anulacion.id_carrera_origen', '=', $idCarrera)
            ->where( 'estudiante_anulacion.activo', '=' , true )
            ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC')
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }
}
