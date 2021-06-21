<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\Models\CambioCarrera;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use App\utils\Tipotramite;

class CambioCarreraController extends Controller
{
    public function getListaCambioCarrera($idEstudiante)
    {
        $arrayCamposSelect = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',

            'cambio_carrera.id_cambio_carrera AS idCambioCarrera',
            'cambio_carrera.id_carrera_origen AS idCarreraOrigen',
            'carrera.nombre as carreraOrigen',
            'cambio_carrera.id_carrera_destino AS idCarreraDestino',
            DB::raw('( select nombre as carreraDestino from carrera where carrera.id_carrera = cambio_carrera.id_carrera_destino)'),

            'cambio_carrera.fecha_solicitud as fechaSolicitud',
            'cambio_carrera.motivo',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

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
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::CAMBIO_DE_CARRERA )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function getDatosParaImpresionFormularioCambioCarrera($idCambioCarrera, $idEstudiante)
    {
        $arrayCamposSelect = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            DB::raw( "( CONCAT(estudiante.paterno, ' ', estudiante.materno, ' ', estudiante.nombres) ) as nombrecompleto"),
            'carrera.nombre as carreraOrigen',
            DB::raw('( select nombre as carreraDestino from carrera where carrera.id_carrera = cambio_carrera.id_carrera_destino)'),
            DB::raw("(SELECT COUNT( * ) as cantidadtraspasosrealizados FROM estudiante_tramite WHERE estudiante_tramite.id_estudiante = $idEstudiante AND estudiante_tramite.id_traspaso > 0 )"),
            'cambio_carrera.id_cambio_carrera as idCambioCarrera',
            'cambio_carrera.fecha_solicitud as fechaSolicitud',
            'cambio_carrera.motivo',
            DB::raw("( SELECT floor(random() * ( 100 - 1 + 1) + 1)::integer  AS materiasAprobadas )"),
            DB::raw("( SELECT floor(random() * ( 80 - 1 + 1) + 1)::integer  AS materiasReprobadas )"),
            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',
            DB::raw("(select costo as \"costoTramite\" from costo where id_costo = ".Tipotramite::CAMBIO_DE_CARRERA.")"),
            DB::raw("( select CONCAT( id_periodo, '/',  id_gestion ) as periodo from periodo_gestion where estado = true)")
        ];

        $estudiante = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('cambio_carrera', 'cambio_carrera.id_cambio_carrera', '=', 'estudiante_tramite.id_cambio_carrera')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_cambio_carrera', '=' , $idCambioCarrera )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante->first(),
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addCambioCarrera(Request $request)
    {
        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $cambioCarrera = new CambioCarrera();
        $cambioCarrera->id_carrera_origen  = $request->input( 'idCarreraOrigen' );
        $cambioCarrera->id_carrera_destino = $request->input( 'idCarreraDestino' );
        $cambioCarrera->fecha_solicitud    = date('Y-m-d H:i:s');
        $cambioCarrera->motivo             = $request->input( 'motivo' );
        $cambioCarrera->convalidacion      = false;
        $cambioCarrera->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $cambioCarrera->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia);

        return response()->json([
            'data'    => $cambioCarrera,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);

    }

    public function updateCambioCarrera(Request $request)
    {
        $idCambioCarrera = $request->input( 'idCambioCarrera' );
        $convalidacion = $request->input( 'convalidacion' );

        $cambioCarrera = CambioCarrera::find( $idCambioCarrera );
        $cambioCarrera->convalidacion = $convalidacion;
        $cambioCarrera->save();

        return response()->json([
            'data'    => null,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK);

    }
}
