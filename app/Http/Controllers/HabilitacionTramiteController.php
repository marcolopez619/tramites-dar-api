<?php

namespace App\Http\Controllers;

use App\utils\Estado;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PeriodoGestion;
use Illuminate\Support\Facades\DB;
use App\Models\HabilitacionTramite;
use DateTime;
use Illuminate\Support\Facades\Date;

class HabilitacionTramiteController extends Controller
{

    public function getListHabilitacionTramite(){
        $this->actualizarEstadoHabiitacionTramite();

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
                                ->where( DB::raw('( habilitacion_tramite.fecha_final::DATE )'), '>=' , DB::raw( '( CURRENT_DATE )' ) )
                                // ->where( 'habilitacion_tramite.fecha_final', '>=' , date('Y-m-d') )

                                ->orderBy( 'habilitacion_tramite.fecha_inicial' , 'DESC' )
                                ->get();

        // Verifica si ya fenecio la fecha final de hablitacion del tramite, para desabilitar el boton de edicion.
        /* if ( !$listaHabilitaciones->isEmpty() ) {

            foreach ($listaHabilitaciones as $item) {

                $selectColumn = [ DB::raw("( select true as desabilitarEdicion from habilitacion_tramite ht where ht.id_hab_tramite = $item->idHabilitacionTramite and ht.fecha_final::date >= CURRENT_DATE )") ];

                $resp = DB::table( 'habilitacion_tramite' )->select( $selectColumn )->get();

                $item->desabilitarEdicion = $resp->first()->desabilitaredicion ?? false;
            }
        } */

        return response()->json( [
            'data'    => $listaHabilitaciones->isEmpty() ? null :  $listaHabilitaciones,
            'message' => $listaHabilitaciones->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramite( Request $request ){
        $tramite = Tramite::find( $request->input( 'idTramite' ) );

        $fechaInicial = $request->input( 'fechaInicial' );
        $fechaFinal = $request->input( 'fechaFinal' );

        $existeChoquesIntervalos = $this->verificarIntervalosHabilitacionTramites(  $request->input( 'idTramite' ), $fechaInicial, $fechaFinal );

        if ( $existeChoquesIntervalos ) {
            return response()->json( [
                'data'    =>null,
                'message' => 'EL TRAMITE YA ESTA HABILITADO HASTA EL : '.( new DateTime($fechaFinal ) )->format( 'd-m-Y' ),
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        }

        $nuevaHabilitacion                     = new HabilitacionTramite();
        $nuevaHabilitacion->fecha_inicial      = $request->input( 'fechaInicial' );
        $nuevaHabilitacion->fecha_final        = $request->input( 'fechaFinal' );
        $nuevaHabilitacion->estado             = $request->input( 'estado' );
        $nuevaHabilitacion->id_periodo_gestion = $request->input( 'idPeriodoGestion' );
        $nuevaHabilitacion->id_tipo_carrera    = $request->input( 'idTipoCarrera' );
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
            $habilitacion->id_tipo_carrera    = $request->input( 'idTipoCarrera' );
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

    private function actualizarEstadoHabiitacionTramite(){
        db::table( 'habilitacion_tramite' )
                ->where( DB::raw('( habilitacion_tramite.fecha_final::DATE )'), '<' , DB::raw( '( CURRENT_DATE )' ) )
                ->update( [ 'estado' => 0 ]);
    }

    public function verificarIntervalosHabilitacionTramites($idTipoTramite, $fechaInicial, $fechaFinal ){

        $inputFechaInicial = new DateTime($fechaInicial );
        $inputFechaFinal = new DateTime($fechaFinal );

        $resp = false;
        $resp2 = false;

        $listaHabilitaciones = HabilitacionTramite::where( 'id_tramite', '=', $idTipoTramite )->where( 'estado', '=' , 1 )->get();

        foreach ($listaHabilitaciones as $item) {

            $fechaInicialBD = new DateTime($item->fecha_inicial );
            $fechaFinalBD = new DateTime($item->fecha_final );


            if ( $inputFechaInicial->format("Y-m-d") >= $fechaInicialBD->format("Y-m-d") && $inputFechaInicial->format("Y-m-d") <= $fechaFinalBD->format("Y-m-d") ) {
                $resp = true;
            }
            if ( $inputFechaFinal->format("Y-m-d") >= $fechaInicialBD->format("Y-m-d") && $inputFechaFinal->format("Y-m-d") <= $fechaFinalBD->format("Y-m-d") ) {
                $resp2 = true;
            }

            if ( $resp || $resp2) {
                return true;
            }
        }

        return false;

    }

}
