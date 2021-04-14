<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\HabilitacionTramite;

class HabilitacionTramiteController extends Controller
{

    public function getListHabilitacionTramite(){

        $listaHabilitaciones = DB::table( 'tramite' )
                                ->join( 'habilitacion_tramite', 'tramite.id_tramite' , '=' , 'habilitacion_tramite.id_tramite' )
                                ->get();

        return response()->json( [
            'data'    => empty( $listaHabilitaciones ) ? null :  $listaHabilitaciones,
            'message' => empty( $listaHabilitaciones ) ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramite( Request $request ){
        // $nuevaHabilitacion = HabilitacionTramite::create( $request->all() );
        $tramite = Tramite::find( $request->input( 'idTramite' ) );

        $nuevaHabilitacion = new HabilitacionTramite();
        $nuevaHabilitacion->fecha_inicial = $request->input( 'fechaInicial' );
        $nuevaHabilitacion->fecha_final = $request->input( 'fechaFinal' );
        $nuevaHabilitacion->estado = $request->input( 'estado' );
        $nuevaHabilitacion->gestion = $request->input( 'gestion' );
        $nuevaHabilitacionCreada = $tramite->habilitacionTramite()->save( $nuevaHabilitacion );

        return response()->json( [
            'data'    => $nuevaHabilitacionCreada,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }


    public function updateHabilitacionTramite( Request $request ){

        $habilitacion = HabilitacionTramite::find( $request->input( 'idHabilitacionTramite' ));

        if( !empty($habilitacion ) )
        {
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
        else
        {
            return response()->json( [
                'data'    => null,
                'message' => 'NO SE ENCONTRÓ LA INFORMACION A ACTUALIZAR EN LA BASE DE DATOS',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        }


    }

}
