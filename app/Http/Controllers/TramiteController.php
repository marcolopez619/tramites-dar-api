<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HabilitacionTramite;
use App\Models\Tramite;
use Illuminate\Support\Facades\DB;

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
}