<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\Estudiante;
use App\Models\PeriodoGestion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\utils\Tipotramite;
use Illuminate\Support\Facades\DB;

class AnulacionController extends Controller
{
    public function getListaAnulacion($idEstudiante)
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
            'estudiante.sexo',

            'anulacion.id_anulacion AS idAnulacion',
            'anulacion.fecha_solicitud AS fechaSolicitud',
            'anulacion.id_carrera_origen AS idCarreraOrigen',
            'carrera.nombre AS carrera',

            'motivo.id_motivo as idMotivo',
            'motivo.descripcion as motivo',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'costo.costo as costoTramite',
            DB::raw("( select CONCAT( id_periodo, '/',  id_gestion ) as periodo from periodo_gestion where estado = true)"),

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $estudiante = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('anulacion', 'anulacion.id_anulacion', '=', 'estudiante_tramite.id_anulacion')
            ->join( 'motivo', 'motivo.id_motivo', '=', 'anulacion.id_motivo' )

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join( 'costo', 'costo.id_costo', '=', 'tramite.id_costo' )
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::ANULACION )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addAnulacion(Request $request)
    {

        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));
        $periodoGestionActual = PeriodoGestion::select( 'id_periodo_gestion' )->where( 'periodo_gestion.estado', '=', true )->first();

        $anulacion                     = new Anulacion();
        $anulacion->fecha_solicitud    = date('Y-m-d H:i:s');
        $anulacion->id_carrera_origen  = $request->input( 'idCarreraOrigen' );
        $anulacion->id_motivo          = $request->input( 'idMotivo' );
        $anulacion->id_periodo_gestion = $periodoGestionActual->id_periodo_gestion;
        $anulacion->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $anulacion->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia );


        return response()->json([
            'data'    => $anulacion,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
