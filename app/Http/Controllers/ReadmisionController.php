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
            /* 'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo', */

            'readmision.id_readmision AS idReadmision',
            'readmision.id_carrera AS idCarrera',
             DB::raw('(SELECT nombre from carrera c where c.id_carrera = readmision.id_carrera) AS carrera'),
            'readmision.fecha_solicitud AS fechaSolicitudReadmision',
            'readmision.motivo',

            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite',
            'estado.id_estado AS estado'
        ];

        $dataComplementaria = DB::table('estudiante')
            ->join('readmision', 'readmision.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite' , '=' , 4 ) // FIXME: Dato quemado el tipo de tramite.
            ->distinct()
            ->orderBy( 'readmision.id_readmision' , 'DESC' )
            ->get();


        if (!$dataComplementaria->isEmpty()) {

            //** Busca la suspencion de la readmision encontrada
            foreach ($dataComplementaria as $item) {

                $suspencion = Readmision::find( $item->idReadmision )->suspencion;

                // Renombra los keys a camelCase
                $suspencion = [
                    'idSuspencion'     => $suspencion->id_suspencion,
                    'idCarrera'        => $suspencion->id_carrera,
                    'tiempoSolicitado' => $suspencion->tiempo_solicitado,
                    'descripcion'      => $suspencion->descripcion,
                    'fechaSolicitud'   => $suspencion->fecha_solicitud,
                    'motivo'           => $suspencion->motivo,
                    'idEstudiante'     => $suspencion->id_estudiante,
                ];

                $item->suspencion = [ $suspencion ];
            }

        }

        return response()->json([
            'data'    => $dataComplementaria->isEmpty() ? null : $dataComplementaria,
            'message' => $dataComplementaria->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
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
        $nuevaReadmision = Readmision::create($arrayDataReadmision);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones'),
            'id_tipo_tramite'=> $nuevaReadmision->id_readmision
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
