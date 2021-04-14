<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HabilitacionTramite;

class HabilitacionTramiteController extends Controller
{

    public function getListHabilitacionTramite(){

        $listaHabilitaciones = HabilitacionTramite::all()->sortBy( 'fecha_final' );

        return response()->json( [
            'data'    => $listaHabilitaciones->isEmpty() ? null :  $listaHabilitaciones,
            'message' => $listaHabilitaciones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramite( Request $request ){
        $nuevaHabilitacion = HabilitacionTramite::create( $request->all() );

        return response()->json( [
            'data'    => $nuevaHabilitacion,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }


    public function updateHabilitacionTramite( Request $request ){

        $habilitacion = HabilitacionTramite::find( $request->input( 'idHabilitacionTramite' ));
        $habilitacion->fecha_inicial = $request->input( 'fechaInicial' );
        $habilitacion->fecha_final   = $request->input( 'fechaFinal' );
        $habilitacion->estado        = $request->input( 'estado' );
        $habilitacion->gestion       = $request->input( 'gestion' );
        $habilitacion->save();

        return response()->json( [
            'data'    => $habilitacion,
            'message' => 'ACTUALIZACIÓN CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

}
