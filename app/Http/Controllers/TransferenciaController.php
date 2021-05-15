<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

class TransferenciaController extends Controller
{
    public function getListaTransferencia($idEstudiante)
    {
        $arrayCamposSelect = [
            'transferencia.id_transferencia AS idTransferencia',
            'transferencia.id_carrera_origen AS idCarreraOrigen',
            DB::raw("( select carrera.nombre AS carreraOrigen  from carrera WHERE carrera.id_carrera = transferencia.id_carrera_origen)" ),
            'transferencia.id_carrera_destino AS idCarreraDestino',
            DB::raw("( select carrera.nombre AS carreraDestino from carrera  WHERE carrera.id_carrera = transferencia.id_carrera_destino)" ),
            'transferencia.fecha_solicitud AS fechaSolicitud',
            'transferencia.motivo',


            'estudiante_tramite.fecha AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite',
            'estado.id_estado AS estado'
        ];



        $dataComplementaria = DB::table('estudiante')
            ->join('transferencia', 'transferencia.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante')
            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite' , '=' , 5 ) // FIXME: Dato quemado el tipo de tramite.
            ->distinct()
            ->get();

        return response()->json([
            'data'    => $dataComplementaria->isEmpty() ? null : $dataComplementaria,
            'message' => $dataComplementaria->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addTransferencia(Request $request)
    {

        $arrayDataTransferencia = [
            'id_carrera_origen' => $request->input( 'idCarreraOrigen'),
            'id_carrera_destino' => $request->input( 'idCarreraDestino'),
            'fecha_solicitud'   => date('Y-m-d H:i:s'),
            'motivo'            => $request->input('motivo'),
            'id_estudiante'     => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevaTransferencia = Transferencia::create($arrayDataTransferencia);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones'),
            'id_tipo_tramite'=> $nuevaTransferencia->id_transferencia
        ];

        // Retorna un booleano como respuesta de insercion
        $estudianteTramite = EstudianteTramite::insert($dataEstudianteTramite);

        return response()->json([
            'data'    => [
                'Transeferencia'    => $nuevaTransferencia,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
