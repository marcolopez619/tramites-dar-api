<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DirectorModel;
use App\utils\Entidad;
use App\utils\Estado;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\utils\Tipotramite;

class DirectorController extends Controller
{
    public function getTramitesPorAtender($idCarrera){
        $arrayEstadosParaNoMostrar = [ Estado::RECHAZADO, Estado::FINALIZADO ];

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
            //'anulacion.motivo',
            DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = anulacion.id_motivo)'),

            'estudiante_tramite.id_estudiante_tramite as idEstudianteTipoTramiteTablaIntermedia',
            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

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
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_tramite.id_anulacion')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsAnulaciones )
            ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
            ->where( 'anulacion.id_carrera_origen', '=', $idCarrera)
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar );





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
                // 'cambio_carrera.motivo',
                DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = cambio_carrera.id_motivo)'),

                'estudiante_tramite.id_estudiante_tramite as idEstudianteTipoTramiteTablaIntermedia',
                'estudiante_tramite.fecha_proceso AS fechaProceso',
                'estudiante_tramite.observaciones',

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
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
            ->where( 'cambio_carrera.id_carrera_origen', '=', $idCarrera)
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');



        $estudianteCambiosCarreraDestino = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_DESTINO )
            ->where( 'cambio_carrera.id_carrera_destino', '=', $idCarrera)
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');

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
            // 'transferencia.motivo',
            DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = transferencia.id_motivo)'),

            'estudiante_tramite.id_estudiante_tramite as idEstudianteTipoTramiteTablaIntermedia',
            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

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
        ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
        ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')

        ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
        ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
        ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
        ->select( $selectColumnsTransferencia )
        ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
        ->where( 'transferencia.id_carrera_origen', '=', $idCarrera)
        ->where( 'estudiante_tramite.activo', '=' , true )
        ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
        ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $estudianteTransferenciasDestino = DB::table('estudiante')
        ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
        ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
        ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
        ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')

        ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
        ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
        ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
        ->select( $selectColumnsTransferencia )
        ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_DESTINO )
        ->where( 'transferencia.id_carrera_destino', '=', $idCarrera)
        ->where( 'estudiante_tramite.activo', '=' , true )
        ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
        ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
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


    public function getTramitesAtendidos($idCarrera){

        $arrayEstados = [ Estado::APROBADO, Estado::RECHAZADO, Estado::FINALIZADO ];

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
            DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = anulacion.id_motivo)'),

            'estudiante_tramite_historico.id_estudiante_tramite_historico as idEstudianteTipoTramiteTablaIntermedia',
            'estudiante_tramite_historico.fecha_proceso AS fechaProceso',
            'estudiante_tramite_historico.observaciones',

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
            ->join('estudiante_tramite_historico', 'estudiante_tramite_historico.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_tramite_historico.id_anulacion')

            ->join('tramite', 'estudiante_tramite_historico.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite_historico.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite_historico.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsAnulaciones )
            ->where( 'estudiante_tramite_historico.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
            ->where( 'anulacion.id_carrera_origen', '=', $idCarrera)
            ->whereIn( 'estudiante_tramite_historico.id_estado', $arrayEstados );
            // ->get();





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
                // 'cambio_carrera.motivo',
                DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = cambio_carrera.id_motivo)'),

                'estudiante_tramite_historico.id_estudiante_tramite_historico as idEstudianteTipoTramiteTablaIntermedia',
                'estudiante_tramite_historico.fecha_proceso AS fechaProceso',
                'estudiante_tramite_historico.observaciones',

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
            ->join('estudiante_tramite_historico', 'estudiante_tramite_historico.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite_historico.id_cambio_carrera')

            ->join('tramite', 'estudiante_tramite_historico.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite_historico.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite_historico.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_tramite_historico.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
            ->where( 'cambio_carrera.id_carrera_origen', '=', $idCarrera)
            ->whereIn( 'estudiante_tramite_historico.id_estado', $arrayEstados )
            ->orderBy( 'estudiante_tramite_historico.fecha_proceso' , 'DESC');



        $estudianteCambiosCarreraDestino = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite_historico', 'estudiante_tramite_historico.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite_historico.id_cambio_carrera')

            ->join('tramite', 'estudiante_tramite_historico.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite_historico.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite_historico.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsCambioCarrera )
            ->where( 'estudiante_tramite_historico.id_entidad', '=' , 4 ) // FIXME: DATOS QUEMADO
            ->where( 'cambio_carrera.id_carrera_destino', '=', $idCarrera)
            ->whereIn( 'estudiante_tramite_historico.id_estado', $arrayEstados )
            ->orderBy( 'estudiante_tramite_historico.fecha_proceso' , 'DESC');
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
            // 'transferencia.motivo',
            DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = transferencia.id_motivo)'),

            'estudiante_tramite_historico.id_estudiante_tramite_historico as idEstudianteTipoTramiteTablaIntermedia',
            'estudiante_tramite_historico.fecha_proceso AS fechaProceso',
            'estudiante_tramite_historico.observaciones',

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
            ->join('estudiante_tramite_historico', 'estudiante_tramite_historico.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite_historico.id_transferencia')

            ->join('tramite', 'estudiante_tramite_historico.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite_historico.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite_historico.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsTransferencia )
            ->where( 'estudiante_tramite_historico.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_ORIGEN )
            ->where( 'transferencia.id_carrera_origen', '=', $idCarrera)
            ->whereIn( 'estudiante_tramite_historico.id_estado', $arrayEstados )
            ->orderBy( 'estudiante_tramite_historico.fecha_proceso' , 'DESC');


        $estudianteTransferenciasDestino = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite_historico', 'estudiante_tramite_historico.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite_historico.id_transferencia')

            ->join('tramite', 'estudiante_tramite_historico.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite_historico.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite_historico.id_entidad', '=', 'entidad.id_entidad')
            ->select( $selectColumnsTransferencia )
            ->where( 'estudiante_tramite_historico.id_entidad', '=' , Entidad::DIRECTOR_CARRERA_DESTINO )
            ->where( 'transferencia.id_carrera_destino', '=', $idCarrera)
            ->whereIn( 'estudiante_tramite_historico.id_estado', $arrayEstados )
            ->orderBy( 'estudiante_tramite_historico.fecha_proceso' , 'DESC')
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
