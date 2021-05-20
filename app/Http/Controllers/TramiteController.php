<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\Estudiante;
use App\utils\Tipotramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\EstudianteAnulacion;

use App\Models\HabilitacionTramite;
use App\Models\EstudianteAnulacionHistorico;

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
                $estudianteAnulacion = EstudianteAnulacion::find( $idEstudianteTipoTramite );
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

    public function insertDataTablaIntermedia( Request $request ){

        $idTipoTramite           = $request->input( 'idTipoTramite' );
        $idEstudianteTipoTramite = $request->input( 'idEstudianteTipoTramite' );
        $nuevaFilaCreada         = null;

        switch ($idTipoTramite) {

            case Tipotramite::ANULACION:{

                // 1.- BUSCA e INSERTA EN EL HISTÓRICO CORRESPONDIENTE la tupla que corresponda
                $oldEstudianteAnulacion = EstudianteAnulacion::find( $idEstudianteTipoTramite );

                EstudianteAnulacionHistorico::create( $oldEstudianteAnulacion->toArray() );

                // $oldEstudianteAnulacion->destroy();

                /* // 1.- Actuailza el campo : Activo, de la tabla intermedia
                $estudianteAnulacion = EstudianteAnulacion::find( $idEstudianteTipoTramite );
                $estudianteAnulacion->activo = false;
                $estudianteAnulacion->save(); */

                // 2.- Inserta nueva tupla en la tabla intermedia
                /* $dataEstudianteAnulacion = [
                    'id_tramite'    => $request->input( 'idTramite' ),
                    'id_estado'     => $request->input( 'idEstado' ),
                    'id_entidad'    => $request->input( 'idEntidad' ),
                    'observaciones' => $request->input( 'observaciones' ),
                    'id_estudiante' => $request->input( 'idEstudiante' ),
                    'id_anulacion'  => $request->input( 'idAnulacion' ),
                    'activo'        => true
                ]; */
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

                $nuevaFilaCreada = EstudianteAnulacion::create( $dataEstudianteAnulacion );

                // Destruye la fila antigua de la tabla intermedia
                $oldEstudianteAnulacion->delete();

                break;
            }

            case Tipotramite::CAMBIO_DE_CARRERA:{ break; }
            default: break;
        }

        return response()->json( [
            'data'    => $nuevaFilaCreada,
            'message' => 'INSERCIÓN CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }
}
