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
    public function getListaHabilitacionTramitePorExcepcion($idEstudiante){

        $queryTiempoPermitido = "(SELECT replace( (select habilitacion_tramite_por_excepcion.fecha_final::timestamp - habilitacion_tramite_por_excepcion.fecha_inicial::timestamp from habilitacion_tramite_por_excepcion)::varchar , 'days', 'dias' ) AS tiempo)";

        $selectColumns = [

            'estudiante.ci',
            'estudiante.complemento',
            DB::raw( "( SELECT e.paterno || ' ' || e.materno || ' ' || e.nombres AS nombreCompleto FROM estudiante e where e.id_estudiante = $idEstudiante)" ),

            'carrera.nombre as carrera',
            DB::raw( "$queryTiempoPermitido" ),

            'habilitacion_tramite_por_excepcion.fecha_inicial AS fechaInicial',
            'habilitacion_tramite_por_excepcion.fecha_final AS fechaFinal',

            'habilitacion_tramite_por_excepcion.id_estado AS estado',

            'tramite.descripcion as tramite'
        ];

        $listaHabilitacionesPorExcepcion = DB::table( 'estudiante' )
                                ->join( 'estudiante_carrera', 'estudiante_carrera.id_estudiante', '=', 'estudiante.id_estudiante' )
                                ->join( 'carrera', 'carrera.id_carrera', '=', 'estudiante_carrera.id_carrera' )
                                ->join( 'habilitacion_tramite_por_excepcion', 'habilitacion_tramite_por_excepcion.id_estudiante' , '=' , 'estudiante.id_estudiante' )
                                ->join( 'tramite', 'tramite.id_tramite', '=' , 'habilitacion_tramite_por_excepcion.id_tramite')
                                ->select( $selectColumns )
                                ->where( 'habilitacion_tramite_por_excepcion.id_estudiante', '=', $idEstudiante )
                                ->distinct()
                                ->orderBy( 'habilitacion_tramite_por_excepcion.fecha_inicial' , 'DESC' )
                                ->get();

        return response()->json( [
            'data'    => empty( $listaHabilitacionesPorExcepcion ) ? null :  $listaHabilitacionesPorExcepcion,
            'message' => empty( $listaHabilitacionesPorExcepcion ) ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addHabilitacionTramitePorExcepcion( Request $request ){

        $tramite = Tramite::find( $request->input( 'idTramite' ) );

        $nuevaHabilitacionPorExcepcion = new HabilitacionTramitePorExcepcion();
        $nuevaHabilitacionPorExcepcion->fecha_inicial = $request->input( 'fechaInicial' );
        $nuevaHabilitacionPorExcepcion->fecha_final   = $request->input( 'fechaFinal' );
        $nuevaHabilitacionPorExcepcion->id_estudiante = $request->input( 'idEstudiante' );
        $nuevaHabilitacionPorExcepcion->id_tramite    = $request->input( 'idTramite' );
        $nuevaHabilitacionPorExcepcion->id_estado     = $request->input( 'estado' );

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
