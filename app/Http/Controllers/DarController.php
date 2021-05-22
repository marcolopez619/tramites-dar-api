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

        $respAnulacion = DB::table('estudiante')

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
            ->where( 'estudiante_anulacion.id_anulacion', '<>', 0 )
            ->where( 'estudiante_anulacion.id_estado', '<>' , Estado::FINALIZADO );

            // ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC');


            $arrayCamposSelectCambioCarrera = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'cambio_carrera.id_cambio_carrera AS idTipoTramite',
                'cambio_carrera.fecha_solicitud AS fechaSolicitud',
                'cambio_carrera.id_carrera_origen AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                'cambio_carrera.motivo',

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

            $respCambioCarrera = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_anulacion.id_cambio_carrera')

                ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectCambioCarrera )
                ->where( 'estudiante_anulacion.id_entidad', '=' , 2 ) // FIXME: DATOS QUEMADO
                ->where( 'estudiante_anulacion.activo', '=' , true )
                ->where( 'estudiante_anulacion.id_cambio_carrera', '<>', 0 )
                ->where( 'estudiante_anulacion.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC')
                ->union($respAnulacion)
                ->get();

        return response()->json([
            'data'    => $respCambioCarrera->isEmpty() ? null : $respCambioCarrera,
            'message' => $respCambioCarrera->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }
}
