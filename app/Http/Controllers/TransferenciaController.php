<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;
use App\utils\Tipotramite;

class TransferenciaController extends Controller
{
    public function getListaTransferencia($idEstudiante)
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

            'transferencia.id_transferencia AS idTransferencia',
            'transferencia.id_carrera_origen AS idCarreraOrigen',
            'carrera.nombre as carreraOrigen',
            'transferencia.id_carrera_destino AS idCarreraDestino',
            DB::raw('( select nombre as carreraDestino from carrera where carrera.id_carrera = transferencia.id_carrera_destino)'),

            'transferencia.fecha_solicitud as fechaSolicitud',
            'transferencia.motivo',


            'estudiante_anulacion.fecha_proceso AS fechaProceso',
            'estudiante_anulacion.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS estado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $listaTransferencias = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_anulacion', 'estudiante_anulacion.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_anulacion.id_transferencia')

            ->join('tramite', 'estudiante_anulacion.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_anulacion.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_anulacion.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_anulacion.id_tramite', '=' , Tipotramite::TRANSFERENCIA )
            ->where( 'estudiante_anulacion.activo', '=' , true )
            ->orderBy( 'estudiante_anulacion.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaTransferencias->isEmpty() ? null : $listaTransferencias,
            'message' => $listaTransferencias->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addTransferencia(Request $request)
    {

        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));

        $transferencia = new transferencia();
        $transferencia->id_carrera_origen  = $request->input( 'idCarreraOrigen' );
        $transferencia->id_carrera_destino = $request->input( 'idCarreraDestino' );
        $transferencia->fecha_solicitud    = date('Y-m-d H:i:s');
        $transferencia->motivo             = $request->input( 'motivo' );
        $transferencia->convalidacion      = false;
        $transferencia->save();

        $dataTablaIntermedia = [
            'id_tramite'    => $request->input( 'idTramite' ),
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'observaciones' => $request->input( 'observaciones' )
        ];

        $transferencia->estudiante()->attach( $estudiante->id_estudiante, $dataTablaIntermedia);

        return response()->json([
            'data'    => $transferencia,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
