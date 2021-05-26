<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Suspencion;
use App\utils\Tipotramite;
use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

class SuspencionController extends Controller
{
    public function getListaSuspenciones($idEstudiante)
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

            'suspencion.id_suspencion AS idSuspencion',
            'suspencion.id_carrera AS idCarrera',
            'carrera.nombre as carrera',
            'suspencion.tiempo_solicitado AS tiempoSolicitado',
            'suspencion.descripcion as descripcionMotivo',
            'suspencion.fecha_solicitud as fechaSolicitud',

            'motivo.descripcion',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $listaSuspenciones = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('suspencion', 'suspencion.id_suspencion', '=', 'estudiante_tramite.id_suspencion')
            ->join('motivo', 'motivo.id_motivo', '=', 'suspencion.id_motivo')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where( 'estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::SUSPENCION )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaSuspenciones->isEmpty() ? null : $listaSuspenciones,
            'message' => $listaSuspenciones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addSuspencion(Request $request)
    {
        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $suspencion                    = new Suspencion();
        $suspencion->id_carrera        = $request->input( 'idCarrera' );
        $suspencion->tiempo_solicitado = $request->input( 'tiempoSolicitado' );
        $suspencion->descripcion       = $request->input( 'descripcion' );
        $suspencion->fecha_solicitud   = date('Y-m-d H:i:s');
        $suspencion->id_motivo         = $request->input( 'idMotivo' );
        $suspencion->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $suspencion->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia);

        return response()->json([
            'data'    => $suspencion,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);

    }
}
