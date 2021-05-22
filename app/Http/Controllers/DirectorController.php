<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectorModel;
use App\utils\Estado;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\utils\Tipotramite;

class DirectorController extends Controller
{
    public function getTramitesPorAtender($idCarrera){
        $selectColumnsAnulaciones = [
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

        $estudianteAnulaciones = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_anulacion.id_anulacion')

            ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsAnulaciones )
            ->where( 'estudiante_anulacion.id_entidad', '=' , 3 ) // FIXME: DATOS QUEMADO
            ->where( 'anulacion.id_carrera_origen', '=', $idCarrera)
            ->where( 'estudiante_anulacion.activo', '=' , true );




        $selectColumnsCambioCarrera = [
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

        $estudianteCambiosCarreraOrigen = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_anulacion.id_cambio_carrera')

            ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_anulacion.id_entidad', '=' , 3 ) // FIXME: DATOS QUEMADO
            ->where( 'cambio_carrera.id_carrera_origen', '=', $idCarrera)
            ->where( 'estudiante_anulacion.activo', '=' , true )
            ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC');



        $estudianteCambiosCarreraDestino = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_anulacion.id_cambio_carrera')

            ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_anulacion.id_entidad', '=' , 4 ) // FIXME: DATOS QUEMADO
            ->where( 'cambio_carrera.id_carrera_destino', '=', $idCarrera)
            ->where( 'estudiante_anulacion.activo', '=' , true )
            ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC');
            // ->union( $estudianteAnulaciones )
            // ->union( $estudianteCambiosCarreraOrigen )
            // ->get();

        ///////////////////

        $selectColumnsTransferencia = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo',

            'transferencia.id_transferencia AS idTipoTramite',
            'transferencia.fecha_solicitud AS fechaSolicitud',
            'transferencia.id_carrera_origen AS idCarreraOrigen',
            'carrera.nombre AS carrera',
            'transferencia.motivo',

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

        $estudianteTransferenciasOrigen = DB::table('estudiante')
        ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
        ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
        ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
        ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_anulacion.id_transferencia')

        ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
        ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
        ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
        ->select( $selectColumnsTransferencia )
        ->where( 'estudiante_anulacion.id_entidad', '=' , 3 ) // FIXME: DATOS QUEMADO
        ->where( 'transferencia.id_carrera_origen', '=', $idCarrera)
        ->where( 'estudiante_anulacion.activo', '=' , true )
        ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC');
        //->union( $estudianteAnulaciones )
        //->union( $estudianteCambiosCarreraOrigen )
        //->union( $estudianteCambiosCarreraDestino )
        //->get();

        $estudianteTransferenciasDestino = DB::table('estudiante')
        ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
        ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
        ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
        ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_anulacion.id_transferencia')

        ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
        ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
        ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
        ->select( $selectColumnsTransferencia )
        ->where( 'estudiante_anulacion.id_entidad', '=' , 4 ) // FIXME: DATOS QUEMADO
        ->where( 'transferencia.id_carrera_destino', '=', $idCarrera)
        ->where( 'estudiante_anulacion.activo', '=' , true )
        ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC')
        ->union( $estudianteAnulaciones )
        ->union( $estudianteCambiosCarreraOrigen )
        ->union( $estudianteCambiosCarreraDestino )
        ->union( $estudianteTransferenciasOrigen )
        ->get();






        return response()->json([
            'data'    => $estudianteTransferenciasDestino->isEmpty() ? null : $estudianteTransferenciasDestino,
            'message' => $estudianteTransferenciasDestino->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }
}
