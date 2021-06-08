<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Readmision;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use App\Models\Motivo;
use Illuminate\Support\Facades\DB;
use App\utils\Tipotramite;

class ReadmisionController extends Controller
{
    public function getListaReadmisiones($idEstudiante)
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

            'readmision.id_readmision AS idReadmision',
            'readmision.id_carrera AS idCarrera',
            'carrera.nombre as carrera',
            'readmision.fecha_solicitud as fechaSolicitud',
            'readmision.motivo',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $listaReadmisiones = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('readmision', 'readmision.id_readmision', '=', 'estudiante_tramite.id_readmision')
            // ->join('motivo', 'motivo.id_motivo', '=', 'readmision.id_motivo')

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where( 'estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::READMISION )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();


        if (!$listaReadmisiones->isEmpty()) {

            //** Busca la suspencion de la readmision encontrada
            foreach ($listaReadmisiones as $item) {

                $suspencion = Readmision::find( $item->idReadmision )->suspencion;

                // Renombra los keys a camelCase
                $suspencion = [
                    'idSuspencion'     => $suspencion->id_suspencion,
                    'idCarrera'        => $suspencion->id_carrera,
                    'tiempoSolicitado' => $suspencion->tiempo_solicitado,
                    'descripcion'      => $suspencion->descripcion,
                    'fechaSolicitud'   => $suspencion->fecha_solicitud,
                    'motivo'           => $suspencion->motivo
                ];

                $item->suspencion = [ $suspencion ];
            }

        }

        return response()->json([
            'data'    => $listaReadmisiones->isEmpty() ? null : $listaReadmisiones,
            'message' => $listaReadmisiones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function getDatosParaImpresionFormularioReadmision( $idReadmision, $idEstudiante)
    {
        $arrayCamposSelect = [
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            DB::raw( "( CONCAT(estudiante.paterno, ' ', estudiante.materno, ' ', estudiante.nombres) ) as nombrecompleto"),

            'carrera.nombre as carrera',
            'facultad.nombre as facultad',

            DB::raw("( SELECT concat( floor(random() * ( 2 - 1 + 1) + 1) , '/', '2021' ) AS periodo )"),

            'readmision.id_readmision as idReadmision',
            'readmision.fecha_solicitud as fechaSolicitud',
            'readmision.motivo',

        ];

        $listaReadmisiones = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('facultad', 'facultad.id_facultad', '=', 'carrera.id_facultad' )
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('readmision', 'readmision.id_readmision', '=', 'estudiante_tramite.id_readmision')

            ->select( $arrayCamposSelect )
            ->where( 'estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_readmision', '=' , $idReadmision )
            ->get();


        if (!$listaReadmisiones->isEmpty()) {

            //** Busca la suspencion de la readmision encontrada
            foreach ($listaReadmisiones as $item) {

                $suspencion = Readmision::find( $item->idReadmision )->suspencion;
                $motivo = Motivo::find( $suspencion->id_motivo)->first();

                // Renombra los keys a camelCase
                $suspencion = [
                    'idSuspencion'     => $suspencion->id_suspencion,
                    'idCarrera'        => $suspencion->id_carrera,
                    'tiempoSolicitado' => $suspencion->tiempo_solicitado,
                    'descripcion'      => $suspencion->descripcion,
                    'fechaSolicitud'   => $suspencion->fecha_solicitud,
                    'motivo'           => $motivo->descripcion
                ];

                $item->suspencion = $suspencion;
            }

        }

        return response()->json([
            'data'    => $listaReadmisiones->isEmpty() ? null : $listaReadmisiones->first(),
            'message' => $listaReadmisiones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addReadmision(Request $request)
    {

        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $readmision                  = new Readmision();
        $readmision->id_carrera      = $request->input( 'idCarrera' );
        $readmision->fecha_solicitud = date('Y-m-d H:i:s');
        $readmision->motivo          = $request->input( 'motivo' );
        $readmision->id_suspencion   = $request->input( 'idSuspencion' );
        $readmision->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $readmision->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia);

        return response()->json([
            'data'    => $readmision,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);



    }
}
