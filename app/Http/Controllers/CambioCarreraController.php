<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CambioCarrera;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class CambioCarreraController extends Controller
{
    public function getListaCambioCarrera($idEstudiante)
    {
        $arrayCamposSelect = [
            'cambio_carrera.id_cambio_carrera AS idCambioCarrera',
            'cambio_carrera.id_carrera_origen AS idCarreraOrigen',
            DB::raw("( select carrera.nombre AS carreraOrigen  from carrera WHERE carrera.id_carrera = cambio_carrera.id_carrera_origen)" ),
            'cambio_carrera.id_carrera_destino AS idCarreraDestino',
            DB::raw("( select carrera.nombre AS carreraDestino from carrera  WHERE carrera.id_carrera = cambio_carrera.id_carrera_destino)" ),
            'cambio_carrera.fecha_solicitud AS fechaSolicitud',
            'cambio_carrera.motivo',

            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite',
            'estado.id_estado AS estado'
        ];

        $estudiante = DB::table('estudiante')
            ->join('cambio_carrera', 'estudiante.id_estudiante', '=', 'cambio_carrera.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite' , '=' , 2 ) // FIXME: Dato quemado el tipo de tramite.
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante,
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addCambioCarrera(Request $request)
    {

        $arrayDataCambioCarrera = [
            'id_carrera_origen'  => $request->input( 'idCarreraOrigen'),
            'id_carrera_destino' => $request->input( 'idCarreraDestino'),
            'fecha_solicitud'    => date('Y-m-d H:i:s'),
            'motivo'             => $request->input('motivo'),
            'id_estudiante'      => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevoCambioCarrera = CambioCarrera::create($arrayDataCambioCarrera);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones'),
            'id_tipo_tramite'=> $nuevoCambioCarrera->id_cambio_carrera
        ];

        // Retorna un booleano como respuesta de insercion
        $estudianteTramite = EstudianteTramite::insert($dataEstudianteTramite);

        return response()->json([
            'data'    => [
                'CambioCarrera'     => $nuevoCambioCarrera,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
