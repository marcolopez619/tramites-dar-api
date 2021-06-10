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
use App\utils\Entidad;
use Illuminate\Support\Facades\DB;

class DarController extends Controller
{
    public function getTramitesPorAtender(){

        $arrayEstadosParaNoMostrar = [ Estado::RECHAZADO, Estado::FINALIZADO ];

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
            // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respAnulacion = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_tramite.id_anulacion')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelectAnulacion )
            ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->where( 'estudiante_tramite.id_anulacion', '<>', 0 )
            ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar );
            // ->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO );


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
                // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respCambioCarrera = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectCambioCarrera )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_cambio_carrera', '<>', 0 )
                ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
                //->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectTransferencias = [
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
                // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respTransferencias = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectTransferencias )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_transferencia', '<>', 0 )
                ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
                // ->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectSuspenciones = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'suspencion.id_suspencion AS idTipoTramite',
                'suspencion.fecha_solicitud AS fechaSolicitud',
                'suspencion.id_carrera AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                'suspencion.descripcion as motivo',
                // 'suspencion.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respSuspenciones = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('suspencion', 'suspencion.id_suspencion', '=', 'estudiante_tramite.id_suspencion')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectSuspenciones )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_suspencion', '<>', 0 )
                ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
                //->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectReadmisiones = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'readmision.id_readmision AS idTipoTramite',
                'readmision.fecha_solicitud AS fechaSolicitud',
                'readmision.id_carrera AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                'readmision.motivo',
                // 'readmision.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respReadmisiones = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('readmision', 'readmision.id_readmision', '=', 'estudiante_tramite.id_readmision')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectReadmisiones )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_readmision', '<>', 0 )
                ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
                // ->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');



        $arrayCamposSelectTraspasos = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'traspaso.id_traspaso AS idTipoTramite',
                'traspaso.fecha_solicitud AS fechaSolicitud',
                'traspaso.id_carrera_origen AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = traspaso.id_motivo)'),
                // 'traspaso.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respTraspasos = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('traspaso', 'traspaso.id_traspaso', '=', 'estudiante_tramite.id_traspaso')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectTraspasos )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_traspaso', '<>', 0 )
                ->whereNotIn( 'estudiante_tramite.id_estado', $arrayEstadosParaNoMostrar )
                // ->where( 'estudiante_tramite.id_estado', '<>' , Estado::FINALIZADO )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
                ->union($respAnulacion)
                ->union($respCambioCarrera)
                ->union($respTransferencias)
                ->union($respSuspenciones)
                ->union($respReadmisiones)
                ->get();

        return response()->json([
            'data'    => $respTraspasos->isEmpty() ? null : $respTraspasos,
            'message' => $respTraspasos->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }
    public function getTramitesAtendidos(){

        $arrayEstadosMostrar = [ Estado::RECHAZADO, Estado::FINALIZADO ];

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
            // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respAnulacion = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_tramite.id_anulacion')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelectAnulacion )
            ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->where( 'estudiante_tramite.id_anulacion', '<>', 0 )
            ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar );


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
                // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respCambioCarrera = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectCambioCarrera )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_cambio_carrera', '<>', 0 )
                ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectTransferencias = [
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
                // DB::raw('(select 0 as tiempoSolicitado)'),

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

        $respTransferencias = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectTransferencias )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_transferencia', '<>', 0 )
                ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectSuspenciones = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'suspencion.id_suspencion AS idTipoTramite',
                'suspencion.fecha_solicitud AS fechaSolicitud',
                'suspencion.id_carrera AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                'suspencion.descripcion as motivo',
                // 'suspencion.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respSuspenciones = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('suspencion', 'suspencion.id_suspencion', '=', 'estudiante_tramite.id_suspencion')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectSuspenciones )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_suspencion', '<>', 0 )
                ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');


        $arrayCamposSelectReadmisiones = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'readmision.id_readmision AS idTipoTramite',
                'readmision.fecha_solicitud AS fechaSolicitud',
                'readmision.id_carrera AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                'readmision.motivo',
                // 'readmision.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respReadmisiones = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('readmision', 'readmision.id_readmision', '=', 'estudiante_tramite.id_readmision')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectReadmisiones )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_readmision', '<>', 0 )
                ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC');



        $arrayCamposSelectTraspasos = [
                'estudiante.id_estudiante as idEstudiante',
                'estudiante.ru',
                'estudiante.ci',
                'estudiante.complemento',
                'estudiante.paterno',
                'estudiante.materno',
                'estudiante.nombres',
                'estudiante.fecha_nacimiento AS fechaNacimiento',
                'estudiante.sexo',

                'traspaso.id_traspaso AS idTipoTramite',
                'traspaso.fecha_solicitud AS fechaSolicitud',
                'traspaso.id_carrera_origen AS idCarreraOrigen',
                'carrera.nombre AS carrera',
                DB::raw('( select descripcion as motivo from motivo where motivo.id_motivo = traspaso.id_motivo)'),
                // 'traspaso.tiempo_solicitado as tiempoSolicitado', // añadi esta columna

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

        $respTraspasos = DB::table('estudiante')

                ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
                ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
                ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
                ->join('traspaso', 'traspaso.id_traspaso', '=', 'estudiante_tramite.id_traspaso')

                ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
                ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
                ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
                ->select( $arrayCamposSelectTraspasos )
                ->where( 'estudiante_tramite.id_entidad', '=' , Entidad::ENCARGADO_DAR )
                ->where( 'estudiante_tramite.activo', '=' , true )
                ->where( 'estudiante_tramite.id_traspaso', '<>', 0 )
                ->whereIn( 'estudiante_tramite.id_estado', $arrayEstadosMostrar )
                ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
                ->union($respAnulacion)
                ->union($respCambioCarrera)
                ->union($respTransferencias)
                ->union($respSuspenciones)
                ->union($respReadmisiones)
                ->get();

        return response()->json([
            'data'    => $respTraspasos->isEmpty() ? null : $respTraspasos,
            'message' => $respTraspasos->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

}
