<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\utils\Tipotramite;
use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\PeriodoGestion;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;

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

            'motivo.id_motivo as idMotivo',
            'motivo.descripcion as motivo',


            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',

            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tramite',

            'estado.id_estado AS idEstado',
            'estado.descripcion AS estado',

            'entidad.id_entidad AS idEntidad',
            'entidad.descripcion AS entidad'
        ];

        $listaTransferencias = DB::table('estudiante')

            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')
            ->join( 'motivo', 'motivo.id_motivo', '=', 'transferencia.id_motivo' )

            ->join('tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')
            ->join('estado', 'estudiante_tramite.id_estado', '=', 'estado.id_estado')
            ->join('entidad', 'estudiante_tramite.id_entidad', '=', 'entidad.id_entidad')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_tramite', '=' , Tipotramite::TRANSFERENCIA )
            ->where( 'estudiante_tramite.activo', '=' , true )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaTransferencias->isEmpty() ? null : $listaTransferencias,
            'message' => $listaTransferencias->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function getDatosParaImpresionFormularioTransferenciaCarrera($idTransferencia, $idEstudiante)
    {
        $arrayCamposSelect = [
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            DB::raw( "( CONCAT(estudiante.paterno, ' ', estudiante.materno, ' ', estudiante.nombres) ) as nombrecompleto"),
            'carrera.nombre as carreraOrigen',
            DB::raw('( select nombre as carreraDestino from carrera where carrera.id_carrera = transferencia.id_carrera_destino)'),
            DB::raw("(SELECT COUNT( * ) as cantidadtraspasosrealizados FROM estudiante_tramite WHERE estudiante_tramite.id_estudiante = $idEstudiante AND estudiante_tramite.id_traspaso > 0 )"),
            'transferencia.id_transferencia as idTransferencia',
            'transferencia.fecha_solicitud as fechaSolicitud',
            'motivo.id_motivo as idMotivo',
            'motivo.descripcion as motivo',
            DB::raw("( SELECT floor(random() * ( 100 - 1 + 1) + 1)::integer  AS materiasAprobadas )"),
            DB::raw("( SELECT floor(random() * ( 80 - 1 + 1) + 1)::integer  AS materiasReprobadas )"),
            'estudiante_tramite.fecha_proceso AS fechaProceso',
            'estudiante_tramite.observaciones',
            DB::raw("(select costo as \"costoTramite\" from costo where id_costo = ".Tipotramite::TRANSFERENCIA.")"),
            DB::raw("( select CONCAT( id_periodo, '/',  id_gestion ) as periodo from periodo_gestion where estado = true)")
        ];

        $estudiante = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join('carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante_tramite', 'estudiante_tramite.id_estudiante', '=', 'estudiante.id_estudiante' )
            ->join('transferencia', 'transferencia.id_transferencia', '=', 'estudiante_tramite.id_transferencia')
            ->join( 'motivo', 'motivo.id_motivo', '=', 'transferencia.id_motivo' )
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->where( 'estudiante_tramite.id_transferencia', '=' , $idTransferencia )
            ->orderBy( 'estudiante_tramite.fecha_proceso' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante->first(),
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);

    }

    public function addTransferencia(Request $request)
    {

        $estudiante = Estudiante::find( $request->input( 'idEstudiante' ));
        $periodoGestionActual = PeriodoGestion::select( 'id_periodo_gestion' )->where( 'periodo_gestion.estado', '=', true )->first();

        $transferencia                     = new transferencia();
        $transferencia->id_carrera_origen  = $request->input( 'idCarreraOrigen' );
        $transferencia->id_carrera_destino = $request->input( 'idCarreraDestino' );
        $transferencia->fecha_solicitud    = date('Y-m-d H:i:s');
        $transferencia->id_motivo           = $request->input( 'idMotivo' );
        $transferencia->id_periodo_gestion = $periodoGestionActual->id_periodo_gestion;
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
