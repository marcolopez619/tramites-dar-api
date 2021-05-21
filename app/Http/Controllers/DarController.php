<?php

namespace App\Http\Controllers;

use TipoTramite;
use eTipoTramite;
use App\utils\Estado;
use App\Models\Anulacion;
use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

class DarController extends Controller
{
    public function getTramitesPorAtender(){

        $arrayCamposSelectAnulacion = [
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
            ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelectAnulacion )
            ->where( 'estudiante_anulacion.id_entidad', '=' , 2 ) // FIXME: DATOS QUEMADO
            ->where( 'estudiante_anulacion.activo', '=' , true )
            ->where( 'estudiante_anulacion.id_estado', '<>' , Estado::FINALIZADO )
            ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC')
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

        /* $selectColumn = [
            'estudiante.id_estudiante AS idEstudiante',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'carrera.nombre AS carrera',
            'estudiante_tramite.id_estudiante_tramite as idEstudianteTramite',
            'estudiante_tramite.fecha AS fechaSolicitud',
            'estudiante_tramite.id_estado AS estado',
            'estudiante_tramite.id_tipo_tramite AS idTipoTramite',
            'estudiante_tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite'
        ];



        $listaSolicitudes = DB::table('carrera')
            ->join('estudiante_carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join( 'estudiante_tramite', 'estudiante.id_estudiante', '=', 'estudiante_tramite.id_estudiante' )
            ->join( 'tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')

            ->select( $selectColumn )
            ->where( 'estudiante_tramite.id_entidad', '=' , 1 ) // FIXME: el 1, debe ser de la entidad, en este caso del encargado del DAR = 2
            ->distinct()
            ->orderBy( 'estudiante_tramite.fecha' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaSolicitudes->isEmpty() ? null : $listaSolicitudes,
            'message' => $listaSolicitudes->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]); */
    }
}
