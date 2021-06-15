<?php

namespace App\Http\Controllers;

use App\utils\Estado;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PeriodoGestion;
use Illuminate\Support\Facades\DB;
use App\Models\HabilitacionTramite;

class HabilitacionTramiteController extends Controller
{

    public function getListHabilitacionTramite(){

        $selectColumns = [
            'tramite.id_tramite AS idTramite',
            'tramite.descripcion AS descripcionTramite',
            'habilitacion_tramite.id_hab_tramite AS idHabilitacionTramite',
            'habilitacion_tramite.fecha_inicial AS fechaInicial',
            'habilitacion_tramite.fecha_final AS fechaFinal',
            'habilitacion_tramite.estado',
            DB::raw(" ( select CONCAT( periodo_gestion.id_periodo, '/', periodo_gestion.id_gestion) AS gestion FROM periodo_gestion where periodo_gestion.estado = true) "),
            'tipo_carrera.id_tipo_carrera as idTipoCarrera',
            'tipo_carrera.descripcion as tipoCarrera',
        ];

        $listaHabilitaciones = DB::table( 'tramite' )
                                ->join( 'habilitacion_tramite', 'tramite.id_tramite' , '=' , 'habilitacion_tramite.id_tramite' )
                                ->join( 'periodo_gestion', 'periodo_gestion.id_periodo_gestion', '=', 'habilitacion_tramite.id_periodo_gestion' )
                                ->join( 'tipo_carrera', 'tipo_carrera.id_tipo_carrera', '=', 'habilitacion_tramite.id_tipo_carrera' )
                                ->select( $selectColumns )
                                // ->where( 'periodo_gestion.estado', '=', true )
                                // ->where( 'habilitacion_tramite.estado', '=', Estado::ACTIVADO )
                                ->orderBy( 'habilitacion_tramite.fecha_inicial' , 'DESC' )
                                ->get();

        return response()->json( [
            'data'    => $listaHabilitaciones->isEmpty() ? null :  $listaHabilitaciones,
            'message' => $listaHabilitaciones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramite( Request $request ){
        $tramite = Tramite::find( $request->input( 'idTramite' ) );

        $nuevaHabilitacion                     = new HabilitacionTramite();
        $nuevaHabilitacion->fecha_inicial      = $request->input( 'fechaInicial' );
        $nuevaHabilitacion->fecha_final        = $request->input( 'fechaFinal' );
        $nuevaHabilitacion->estado             = $request->input( 'estado' );
        $nuevaHabilitacion->id_periodo_gestion = $request->input( 'idPeriodoGestion' );
        $nuevaHabilitacionCreada               = $tramite->habilitacionTramite()->save( $nuevaHabilitacion );

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
            $habilitacion->fecha_inicial      = $request->input( 'fechaInicial' );
            $habilitacion->fecha_final        = $request->input( 'fechaFinal' );
            $habilitacion->estado             = $request->input( 'estado' );
            $habilitacion->id_tramite         = $request->input( 'idTramite' );
            $habilitacion->id_periodo_gestion = $request->input( 'idPeriodoGestion' );
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
