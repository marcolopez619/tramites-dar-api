<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\EstudianteAnulacion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HabilitacionTramite;
use App\Models\Tramite;
use Illuminate\Support\Facades\DB;

use App\utils\Tipotramite;

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
}
