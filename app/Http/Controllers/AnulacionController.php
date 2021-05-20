<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
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
            'anulacion.motivo',


            'estudiante_anulacion.fecha_proceso AS fechaProceso',
            'estudiante_anulacion.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS estado',
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
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_anulacion.id_tramite', '=' , 1 ) // FIXME: DATOS QUEMADO
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

    public function addAnulacion(Request $request)
    {

        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $anulacion = new Anulacion();
        $anulacion->fecha_solicitud = date('Y-m-d H:i:s');
        $anulacion->motivo = $request->input( 'motivo' );
        $anulacion->id_carrera_origen = $request->input( 'idCarreraOrigen' );
        $anulacion->save();

        $dataTablaIntermedia = [
            'id_tramite' => $request->input( 'idTramite' ),
            'id_estado' => $request->input( 'idEstado' ),
            'id_entidad' => $request->input( 'idEntidad' ),
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
