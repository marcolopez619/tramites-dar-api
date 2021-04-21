<?php

namespace App\Http\Controllers;

use App\Models\Readmision;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

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
            'estudiante.sexo',

            'readmision.id_readmision AS idReadmision',
            'readmision.id_carrera AS idCarrera',
            'readmision.fecha_solicitud AS fechaSolicitud',
            'readmision.motivo',
            'readmision.id_suspencion AS idSuspencion',

            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite',
            'estado.descripcion AS estado'
        ];

        $estudiante = DB::table('estudiante')
            ->join('readmision', 'readmision.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite' , '=' , 4 ) // FIXME: Dato quemado el tipo de tramite.
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addReadmision(Request $request)
    {

        $arrayDataReadmision = [
            'id_carrera'        => $request->input( 'idCarrera'),
            'fecha_solicitud'   => date('Y-m-d H:i:s'),
            'motivo'            => $request->input('motivo'),
            'id_suspencion'     => $request->input('idSuspencion'),
            'id_estudiante'     => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevaReadmision = Readmision::insert($arrayDataReadmision);

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
                'Readmision'        => $nuevaReadmision,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
