<?php

namespace App\Http\Controllers;

use App\Models\Traspaso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

class TraspasosController extends Controller
{
    public function getListaTraspaso($idEstudiante)
    {
        $arrayCamposSelect = [
            /* 'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo', */

            'traspaso.id_traspaso as idTraspaso',

            DB::raw('(select id_universidad AS idUniversidadDestino FROM universidad WHERE id_universidad = traspaso.id_univ_destino)'),
            DB::raw('(select nombre AS nombreUniversidadDestino FROM universidad WHERE id_universidad = traspaso.id_univ_destino)'),
            DB::raw('(select id_carrera AS idCarreraDestino FROM carrera WHERE id_carrera = traspaso.id_carrera_destino)'),
            DB::raw('(select nombre AS nombreCarreraDestino FROM carrera WHERE id_carrera = traspaso.id_carrera_destino)'),

            DB::raw("(select '01/2001' AS periodo)"),


            'traspaso.fecha_solicitud AS fechaSolicitud',
            'traspaso.motivo',
/*
            'traspaso.id_traspaso AS idTraspaso',
            'traspaso.id_carrera_destino AS idCarreraDestino',
            'traspaso.descripcion AS descripcion',
            'traspaso.anio_ingreso AS anioIngreso',
            'traspaso.materias_aprobadas AS materiasAprobadas',
            'traspaso.materias_reprobadas AS materiasReprobadas',


            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite', */

            'estado.id_estado AS estado'
        ];



        $dataComplementaria = DB::table('estudiante')

            // ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante' )
            // ->join('carrera', 'carrera.id_carrera', '=', 'estudiante_carrera.id_carrera' )

            ->join('traspaso', 'traspaso.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            // ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite' , '=' , 6 ) // FIXME: Dato quemado el tipo de tramite.

            ->get();

        return response()->json([
            'data'    => $dataComplementaria->isEmpty() ? null : $dataComplementaria,
            'message' => $dataComplementaria->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addTraspaso(Request $request)
    {

        $arrayDataTraspaso = [
            'id_univ_destino'     => $request->input( 'idUnivDestino'),
            'id_carrera_destino'  => $request->input( 'idCarreraDestino'),
            'descripcion'         => $request->input( 'descripcion'),
            'anio_ingreso'        => $request->input( 'anioIngreso'),
            'materias_aprobadas'  => $request->input( 'materiasAprobadas'),
            'materias_reprobadas' => $request->input( 'materiasReprobadas'),
            'fecha_solicitud'     => date('Y-m-d H:i:s'),
            'motivo'              => $request->input('motivo'),
            'id_estudiante'       => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevoTraspaso = Traspaso::insert($arrayDataTraspaso);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones')
        ];

        // Retorna un booleano como respuesta de insercion
        $estudianteTramite = EstudianteTramite::insert($dataEstudianteTramite);

        return response()->json([
            'data'    => [
                'Traspaso'          => $nuevoTraspaso,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
