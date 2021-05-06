<?php

namespace App\Http\Controllers;

use App\Models\Suspencion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

class SuspencionController extends Controller
{
    public function getListaSuspenciones($idEstudiante)
    {
        $arrayCamposSelect = [
           /*  'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo', */

            'suspencion.id_suspencion AS idSuspencion',
            'suspencion.id_carrera AS idCarrera',
            DB::raw("(SELECT carrera.nombre AS carrera FROm carrera WHERE carrera.id_carrera = suspencion.id_carrera)"),
            'suspencion.tiempo_solicitado AS tiempoSolicitado',
            // 'suspencion.descripcion',
            'suspencion.fecha_solicitud AS fechaSolicitud',
            'suspencion.motivo',

            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite',
            'estado.id_estado AS estado'
        ];

        $estudiante = DB::table('estudiante')
            ->join('suspencion', 'suspencion.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where('estudiante_tramite.id_tramite' , '=' , 3 ) // FIXME: Dato quemado el tipo de tramite.
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addSuspencion(Request $request)
    {

        $arrayDataSuspencion = [
            'id_carrera'        => $request->input( 'idCarrera'),
            'tiempo_solicitado' => $request->input( 'tiempoSolicitado'),
            'descripcion'       => $request->input( 'descripcion'),
            'fecha_solicitud'   => date('Y-m-d H:i:s'),
            'motivo'            => $request->input('idMotivo'), // FIXME: vincular el idMotivo en la BD con la tabla respectiva.
            'id_estudiante'     => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevaSuspencion = Suspencion::create($arrayDataSuspencion);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones'),
            'id_tipo_tramite'=> $nuevaSuspencion->id_suspencion
        ];

        // TODO: FALTA INSERTAR EL MOTIDO EN LA TABLA DE MOTIVOS QUE SE DEBE CREAR.

        // Retorna un booleano como respuesta de insercion
        $estudianteTramite = EstudianteTramite::insert($dataEstudianteTramite);

        return response()->json([
            'data'    => [
                'Suspencion'        => $nuevaSuspencion,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
