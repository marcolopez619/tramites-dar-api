<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\HabilitacionTramitePorExcepcion;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HabilitacionTramitePorExcepcionController extends Controller
{
    public function getListaHabilitacionTramitePorExcepcion(){

        $selectColumns = [

            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',

            'carrera.nombre as carrera',
            'habilitacion_tramite_por_excepcion.id_habilitacion_por_excepcion AS idHabilitacionPorExcepcion',
            'habilitacion_tramite_por_excepcion.fecha_habilitacion AS fechaHabilitacion',
            'habilitacion_tramite_por_excepcion.fecha_regularizacion AS fechaRegularizacion',
            'habilitacion_tramite_por_excepcion.motivo',

            'habilitacion_tramite_por_excepcion.id_estado AS estado',

            'tramite.descripcion as tramite'
        ];

        $listaHabilitacionesPorExcepcion = DB::table( 'estudiante' )
                                ->join( 'estudiante_carrera', 'estudiante_carrera.id_estudiante', '=', 'estudiante.id_estudiante' )
                                ->join( 'carrera', 'carrera.id_carrera', '=', 'estudiante_carrera.id_carrera' )
                                ->join( 'habilitacion_tramite_por_excepcion', 'habilitacion_tramite_por_excepcion.id_estudiante' , '=' , 'estudiante.id_estudiante' )
                                ->join( 'tramite', 'tramite.id_tramite', '=' , 'habilitacion_tramite_por_excepcion.id_tramite')
                                ->join( 'periodo_gestion', 'periodo_gestion.id_periodo_gestion', '=' , 'habilitacion_tramite_por_excepcion.id_periodo_gestion')
                                ->select( $selectColumns )
                                // ->distinct()
                                ->orderBy( 'habilitacion_tramite_por_excepcion.fecha_habilitacion' , 'DESC' )
                                ->get();


        if (!$listaHabilitacionesPorExcepcion->isEmpty() ) {

            foreach ($listaHabilitacionesPorExcepcion as $element) {
                $element->nombrecompleto = $element->paterno.' '.$element->materno.' '.$element->nombres;
                // $element->tiempo         = (strtotime($element->fechaFinal) - strtotime($element->fechaInicial))/60/60/24 + 1;
            }
        }

        return response()->json( [
            'data'    => $listaHabilitacionesPorExcepcion->isEmpty() ? null :  $listaHabilitacionesPorExcepcion,
            'message' => $listaHabilitacionesPorExcepcion->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramitePorExcepcion( Request $request ){

        $tramite = Tramite::find( $request->input( 'idTramite' ) );

        $nuevaHabilitacionPorExcepcion                       = new HabilitacionTramitePorExcepcion();
        $nuevaHabilitacionPorExcepcion->fecha_habilitacion   = date('Y-m-d H:i:s');;
        $nuevaHabilitacionPorExcepcion->fecha_regularizacion = $request->input( 'fechaRegularizacion' );
        $nuevaHabilitacionPorExcepcion->motivo               = $request->input( 'motivoHabilitacion' );
        $nuevaHabilitacionPorExcepcion->id_estudiante        = $request->input( 'idEstudiante' );
        $nuevaHabilitacionPorExcepcion->id_tramite           = $request->input( 'idTramite' );
        $nuevaHabilitacionPorExcepcion->id_estado            = $request->input( 'estado' );
        $nuevaHabilitacionPorExcepcion->id_periodo_gestion   = $request->input( 'idPeriodoGestion' );

        $nuevaHabilitacionCreadaPorExcepcion = $tramite->habilitacionTramitePorExcepcion()->save( $nuevaHabilitacionPorExcepcion );

        return response()->json( [
            'data'    => $nuevaHabilitacionCreadaPorExcepcion,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }

    public function updateHabilitacionTramite( Request $request ){

        $habilitacion = HabilitacionTramitePorExcepcion::find( $request->input( 'idHabilitacionTramitePorExcepcion' ));

        if( !empty($habilitacion ) )
        {
            $habilitacion->fecha_inicial = $request->input( 'fechaInicial' );
            $habilitacion->fecha_final   = $request->input( 'fechaFinal' );
            $habilitacion->id_tramite    = $request->input( 'idTramite' );
            $habilitacion->estado        = $request->input( 'estado' );
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
