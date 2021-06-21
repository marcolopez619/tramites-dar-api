<?php

namespace App\Http\Controllers;

use App\Models\Traspaso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\utils\Tipotramite;

class TraspasosController extends Controller
{
    public function getListaTraspaso($idEstudiante)
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

            'traspaso.id_traspaso AS idTraspaso',
            'traspaso.id_univ_destino AS idUnivDestino',
            DB::raw('(select nombre as universidadDestino from universidad where universidad.id_universidad = traspaso.id_univ_destino)'),
            'traspaso.id_carrera_destino AS idCarrera',
            DB::raw('(select nombre as carreraDestino from carrera where carrera.id_carrera = traspaso.id_carrera_destino)'),
            'traspaso.descripcion as descripcionMotivo',
            'traspaso.anio_ingreso as anioIngreso',
            'traspaso.materias_aprobadas as materiasAprobadas',
            'traspaso.materias_reprobadas as materiasReprobadas',
            'traspaso.fecha_solicitud as fechaSolicitud',
            DB::raw("( SELECT concat( floor(random() * ( 2 - 1 + 1) + 1) , '/', '2021' ) AS periodo )"),

            'motivo.descripcion as motivo',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $listaTraspasos = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('traspaso', 'traspaso.id_traspaso', '=', 'estudiante_tramite.id_traspaso')
            ->join('motivo', 'motivo.id_motivo', '=', 'traspaso.id_motivo')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where( 'estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::TRASPASO_UNIVERSIDAD )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaTraspasos->isEmpty() ? null : $listaTraspasos,
            'message' => $listaTraspasos->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function getDatosParaImpresionFormularioTraspasoUniversidad($idTraspaso, $idEstudiante){
        $arrayCamposSelect = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            DB::raw( "( CONCAT(estudiante.paterno, ' ', estudiante.materno, ' ', estudiante.nombres) ) as nombrecompleto"),

            'traspaso.id_traspaso AS idTraspaso',
            'traspaso.id_univ_destino AS idUnivDestino',
            DB::raw('(select nombre as universidadDestino from universidad where universidad.id_universidad = traspaso.id_univ_destino)'),
            'traspaso.id_carrera_destino AS idCarreraDestino',
            DB::raw('(select nombre as carreraDestino from carrera where carrera.id_carrera = traspaso.id_carrera_destino)'),
            'traspaso.id_carrera_origen AS idCarreraOrigen',
            DB::raw('(select nombre as carreraOrigen from carrera where carrera.id_carrera = traspaso.id_carrera_origen)'),
            DB::raw("(select 'F11 L100 N237' as numeroDiploma )"),
            // DB::raw("( SELECT concat( floor(random() * ( 2 - 1 + 1) + 1) , '/', '2021' ) AS periodo )"),
            DB::raw("(SELECT round( CAST( random() * 100 as numeric ), 2 )  AS promediogeneral )"),
            'traspaso.anio_ingreso as anioIngreso',

            'traspaso.descripcion as descripcionMotivo',
            'traspaso.materias_aprobadas as materiasAprobadas',
            'traspaso.materias_reprobadas as materiasReprobadas',
            'traspaso.fecha_solicitud as fechaSolicitud',

            'motivo.descripcion as motivo',

            DB::raw("(select costo as \"costoTramite\" from costo where id_costo = ".Tipotramite::TRASPASO_UNIVERSIDAD.")"),
            DB::raw("( select CONCAT( id_periodo, '/',  id_gestion ) as periodo from periodo_gestion where estado = true)")
        ];

        $estudiante = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('traspaso', 'traspaso.id_traspaso', '=', 'estudiante_tramite.id_traspaso')
            ->join('motivo', 'motivo.id_motivo', '=', 'traspaso.id_motivo')

            // ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            // ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            // ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where( 'estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_traspaso', '=', $idTraspaso )
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante->first(),
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }


    public function addTraspaso(Request $request)
    {
        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $traspaso                      = new Traspaso();
        $traspaso->id_univ_destino     = $request->input( 'idUnivDestino' );
        $traspaso->id_carrera_destino  = $request->input( 'idCarreraDestino' );
        $traspaso->id_carrera_origen   = $request->input( 'idCarreraOrigen' );
        $traspaso->descripcion         = $request->input( 'descripcion' );
        $traspaso->anio_ingreso        = $request->input( 'anioIngreso' );
        $traspaso->materias_aprobadas  = $request->input( 'materiasAprobadas' );
        $traspaso->materias_reprobadas = $request->input( 'materiasReprobadas' );
        $traspaso->fecha_solicitud     = date('Y-m-d H:i:s');
        $traspaso->id_motivo           = $request->input( 'idMotivo' );
        $traspaso->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $traspaso->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia);

        return response()->json([
            'data'    => $traspaso,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);

    }
}
