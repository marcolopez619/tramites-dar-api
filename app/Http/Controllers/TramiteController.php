<?php

namespace App\Http\Controllers;

use App\utils\Estado;
use App\Models\Tramite;
use App\Models\Estudiante;
use App\utils\Tipotramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;

use Illuminate\Support\Facades\DB;
use App\Models\HabilitacionTramite;
use App\Models\EstudianteTramiteHistorico;
use App\Models\HabilitacionTramitePorExcepcion;
use App\Models\Motivo;

class TramiteController extends Controller
{
    public function getListaTramite(){
        $selectColumns = [
            "tramite.id_tramite as idTramite",
            "tramite.descripcion as descripcionTramite"
        ];

        // $listaTramites = Tramite::select()->sortBy( 'descripcion' );
        $listaTramites = DB::table( 'tramite' )->select( $selectColumns )->orderBy( 'descripcion', 'ASC' )->get();


        return response()->json( [
            'data'    => $listaTramites->isEmpty() ? null : $listaTramites,
            'message' => $listaTramites->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addTramite( Request $request ){
        $nuevoTramite = Tramite::create( $request->all() );

        return response()->json( [
            'data'    => $nuevoTramite,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }

    public function updateTramite( Request $request ){

        $tramite = Tramite::find( $request->input( 'idTramite' ));
        $tramite->descripcion = $request->input( 'descripcion' );
        $tramite->save();

        return response()->json( [
            'data'    => $tramite,
            'message' => 'ACTUALIZACIÓN CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function updateEstadoTramite( Request $request ){

        // Capturamos el id del tipo de tramite del cual se quiere cambiar su estado
        $idTipoTramite           = $request->input( 'idTipoTramite' );
        $idEstudianteTipoTramite = $request->input( 'idEstudianteTipoTramite' );
        $nuevoEstado             = $request->input( 'estado' );

        switch ($idTipoTramite) {
            case Tipotramite::ANULACION: {
                $estudianteAnulacion = EstudianteTramite::find( $idEstudianteTipoTramite );
                $estudianteAnulacion->id_estado = $nuevoEstado;
                $estudianteAnulacion->save();
                break;
            }

            default:
                # code...
                break;
        }

        return response()->json( [
            'data'    => null,
            'message' => 'ACTUALIZACIÓN CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function verificarHabilitacionTramite($idTramite, $idEstudiante){

        $selectColumns = [
            DB::raw("(SELECT true AS istramitehabilitado  FROM habilitacion_tramite where CURRENT_DATE BETWEEN fecha_inicial::date AND fecha_final::date AND estado = 1 and id_tramite = $idTramite)")
        ];

        $respQuery = DB::table( 'habilitacion_tramite' )
        ->select( $selectColumns )
        ->get();

        // $isTramiteHabilitado = [ 'isTramiteHabilitado' => ( empty( $respQuery ) ) ? null: $respQuery[ 0 ]->istramitehabilitado !== null ];
        $isTramiteHabilitado = ( $respQuery->isEmpty() ) ? null: $respQuery[ 0 ]->istramitehabilitado !== null;

        if ( ! $isTramiteHabilitado ) {

            // 1.- Ir a consultar a la tabla : Habilitacion_tramite_por_excepcion, con el idEstudiante, para ver si posee una excepcion para hablitar el boton de nueva solicitud
            $respHabilitacionExcepcion = HabilitacionTramitePorExcepcion::select( '*' )
                                        ->where( 'id_tramite', '=', $idTramite )
                                        ->where( 'id_estudiante', '=', $idEstudiante )
                                        ->where( 'id_estado', '=', Estado::ACTIVADO )
                                        ->get();
            // 2.- Si Existe en la tabla anterior, => se habilita el tramite ( true ), sino se lo desabilita ( false )
            $isTramiteHabilitado = !$respHabilitacionExcepcion->isEmpty();
        }

        return response()->json( [
            'data'    => [ 'isTramiteHabilitado' => $isTramiteHabilitado ],
            'message' => 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function verificarExistenciaTramiteEnCurso($idEstudiante){

        $estados = [ Estado::ENVIADO, Estado::APROBADO ];

        $existenTramitesEnCurso = EstudianteTramite::where( 'estudiante_tramite.id_estudiante', '=', $idEstudiante)
                                                    ->whereIn( 'estudiante_tramite.id_estado', $estados )
                                                    ->count() > 0;

        return response()->json( [
            'data'    => [ 'existenTramitesEnCurso' => $existenTramitesEnCurso ],
            'message' => 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function insertDataTablaIntermedia( Request $request ){

        $idTipoTramite           = $request->input( 'idTipoTramite' );
        $idEstudianteTipoTramite = $request->input( 'idEstudianteTipoTramite' );
        $nuevaFilaCreada         = null;

        // 1.- BUSCA e INSERTA EN EL HISTÓRICO CORRESPONDIENTE la tupla que corresponda
        $oldEstudianteAnulacion = EstudianteTramite::find( $idEstudianteTipoTramite );

        EstudianteTramiteHistorico::create( $oldEstudianteAnulacion->toArray() );

        $dataEstudianteAnulacion = [
            'id_tramite'    => $oldEstudianteAnulacion->id_tramite,
            'id_estado'     => $request->input( 'idEstado' ),
            'id_entidad'    => $request->input( 'idEntidad' ),
            'observaciones' => $request->input( 'observaciones' ),
            'fecha_proceso' => date('Y-m-d H:i:s'),
            'id_estudiante' => $oldEstudianteAnulacion->id_estudiante,
            'activo'        => true,

            // Verifica los keys referenciales, sino existes, => inserta el valor por default de esas tablas o tramites, el cual es CERO
            'id_anulacion'      => $oldEstudianteAnulacion->id_anulacion ?? 0,
            'id_cambio_carrera' => $oldEstudianteAnulacion->id_cambio_carrera ?? 0,
            'id_transferencia'  => $oldEstudianteAnulacion->id_transferencia ?? 0,
            'id_suspencion'     => $oldEstudianteAnulacion->id_suspencion ?? 0,
            'id_readmision'     => $oldEstudianteAnulacion->id_readmision ?? 0,
            'id_traspaso'       => $oldEstudianteAnulacion->id_traspaso ?? 0,
        ];

        $nuevaFilaCreada = EstudianteTramite::create( $dataEstudianteAnulacion );

        // Destruye la fila antigua de la tabla intermedia
        $oldEstudianteAnulacion->delete();


        /* switch ($idTipoTramite) {

            case Tipotramite::ANULACION:{

                // 1.- BUSCA e INSERTA EN EL HISTÓRICO CORRESPONDIENTE la tupla que corresponda
                $oldEstudianteAnulacion = EstudianteTramite::find( $idEstudianteTipoTramite );

                EstudianteTramiteHistorico::create( $oldEstudianteAnulacion->toArray() );

                $dataEstudianteAnulacion = [
                    'id_tramite'    => $oldEstudianteAnulacion->id_tramite,
                    'id_estado'     => $request->input( 'idEstado' ),
                    'id_entidad'    => $request->input( 'idEntidad' ),
                    'observaciones' => $request->input( 'observaciones' ),
                    'fecha_proceso' => date('Y-m-d H:i:s'),
                    'id_estudiante' => $oldEstudianteAnulacion->id_estudiante,
                    'id_anulacion'  => $oldEstudianteAnulacion->id_anulacion,
                    'activo'        => true
                ];

                $nuevaFilaCreada = EstudianteTramite::create( $dataEstudianteAnulacion );

                // Destruye la fila antigua de la tabla intermedia
                $oldEstudianteAnulacion->delete();

                break;
            }

            case Tipotramite::CAMBIO_DE_CARRERA:{ break; }
            default: break;
        } */

        return response()->json( [
            'data'    => $nuevaFilaCreada,
            'message' => 'INSERCIÓN CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function getSeguimientoTramite($idTramite, $idTipoTramite){
        $tableName = null;
        $tableNameId = null;
        $motivo = null;

        switch ($idTipoTramite) {
            case Tipotramite::ANULACION           : $tableName = 'anulacion' ; $tableNameId = 'id_anulacion'; break;
            case Tipotramite::CAMBIO_DE_CARRERA   : $tableName = 'cambio_carrera' ; $tableNameId  = 'id_cambio_carrera'; break;
            case Tipotramite::SUSPENCION          : $tableName = 'suspencion' ; $tableNameId = 'id_suspencion';  break;
            case Tipotramite::READMISION          : $tableName = 'readmision' ; $tableNameId = 'id_readmision'; break;
            case Tipotramite::TRANSFERENCIA       : $tableName = 'transferencia' ; $tableNameId = 'id_transferencia'; break;
            case Tipotramite::TRASPASO_UNIVERSIDAD: $tableName = 'traspaso' ; $tableNameId = 'id_traspaso'; break;
            default: break;
        }

        $selectColumns = [
            'tramite.id_tramite as idTramite',
            'tramite.descripcion as tramite',

            'estado.id_estado as idEstado',
            'estado.descripcion as estado',

            'entidad.id_entidad as idEntidad',
            'entidad.descripcion as entidad',

            'estudiante_tramite.fecha_proceso as fechaProceso',
            'estudiante_tramite.observaciones',

            $tableName.'.'.'id_motivo as idMotivo'

            //($idTipoTramite != TipoTramite::SUSPENCION && $idTipoTramite != Tipotramite::TRASPASO_UNIVERSIDAD ) ? $tableName.'.'.'motivo' : $tableName.'.'.'id_motivo as idMotivo'
        ];

        $seguimiento = DB::table( 'tramite' )
                ->join( 'estudiante_tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite' )
                ->join( 'estado', 'estado.id_estado', '=', 'estudiante_tramite.id_estado' )
                ->join( 'entidad', 'entidad.id_entidad', '=', 'estudiante_tramite.id_entidad' )
                ->join( $tableName, $tableName.'.'.$tableNameId, '=', 'estudiante_tramite'.'.'.$tableNameId )
                ->select($selectColumns)
                ->where('estudiante_tramite'.'.'.$tableNameId, '=', $idTramite)
                ->get();

        $motivo = Motivo::find( $seguimiento->first()->idMotivo );
        $seguimiento->first()->motivo = $motivo->descripcion;

        /* if ($idTipoTramite == TipoTramite::SUSPENCION || $idTipoTramite == TipoTramite::TRASPASO_UNIVERSIDAD){
            $motivo = Motivo::find( $seguimiento[ 0 ]->idMotivo);
            $seguimiento->first()->motivo = $motivo->descripcion;
        } */

        return response()->json( [
            'data'    => $seguimiento->isEmpty() ? null : $seguimiento->first(),
            'message' => $seguimiento->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );

    }
}
