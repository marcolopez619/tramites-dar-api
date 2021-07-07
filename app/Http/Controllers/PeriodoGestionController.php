<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PeriodoGestion;
use Illuminate\Support\Facades\DB;

class PeriodoGestionController extends Controller
{
    public function getPeriodoActivo(){
        $selectColumns = ['id_periodo_gestion as idPeriodoGestion' , 'id_periodo as periodo', 'id_gestion as gestion', 'estado', 'fecha_modificacion as fechaModificacion'];

        $data = PeriodoGestion::select( $selectColumns )->where( 'estado' , '=', true )->get();

        return response()->json( [
            'data'    => $data->isEmpty() ? null :  $data[ 0 ],
            'message' => $data->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getAllPeriodos(){
        $selectColumns = [
            'id_periodo_gestion as idPeriodoGestion' ,
            'id_periodo as periodo', 'id_gestion as gestion',
            DB::raw( "(CASE WHEN estado = true THEN 1 ELSE 0 END) AS estado" ),
            'fecha_modificacion as fechaModificacion'
        ];

        $data = PeriodoGestion::select( $selectColumns )->orderBy( 'fecha_modificacion', 'DESC' )->get();

        return response()->json( [
            'data'    => $data->isEmpty() ? null :  $data,
            'message' => $data->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getCalculoCompraMatriculaToGestionActual($idEstudiante){

        $cantidadPeriodos = 0;
        $cantidadGestiones = 0;

        // 1.- Obtener el periodo que compró la matricula
        $selectColumns = [
            'matricula.id_matricula',
            'matricula.id_periodo_gestion',
            'matricula.id_estudiante',

            'periodo_gestion.id_periodo_gestion',
            'periodo_gestion.id_periodo',
            'periodo_gestion.id_gestion',
            'periodo_gestion.estado',
        ];
        $ultimaGestionComprada = DB::table( 'matricula' )
                                    ->join( 'periodo_gestion', 'periodo_gestion.id_periodo_gestion', '=', 'matricula.id_periodo_gestion')
                                    ->select( $selectColumns )
                                    ->where( 'matricula.id_estudiante' , '=' , $idEstudiante )
                                    ->where( 'matricula.estado' , '=' , true  )
                                    ->get();

        // 2.- Obtener la gestion vigente o activa
        if ( !$ultimaGestionComprada->isEmpty() ) {

            // Realizamos el calculo respectivo
            $gestionVigente = PeriodoGestion::select( '*' )->where( 'estado', '=', true )->first();

            $matriculaUltimaGestionComprada = $ultimaGestionComprada->first();
            $cantidadGestiones =  ( $gestionVigente->id_gestion - $matriculaUltimaGestionComprada->id_gestion ); // Se le resta -1 por la gestion actual

            if ( $matriculaUltimaGestionComprada->id_periodo == 2 ) {
                $cantidadGestiones--; // Se le resta -1 porque su ultimo periodo fue en el segundo semtres del año
            }



            if ( $gestionVigente->id_periodo == 1 ) {

                if ($cantidadGestiones == 1 ) {
                    $cantidadPeriodos = 2;  // 1 gestion es igual a 2 periodos.
                }else{
                    $cantidadPeriodos = abs( ( $cantidadGestiones * 2 ) - 1 ); // Se le resta un periodo
                }

            }


            if ($matriculaUltimaGestionComprada->id_periodo == 2) {
                $cantidadPeriodos = ($cantidadGestiones * 2) + 1;
            }else{
                $cantidadPeriodos = $cantidadGestiones * 2;
            }
        }

        $resultadoCalculo = [
            'cantidadPeriodos' => abs( $cantidadPeriodos ),
            'cantidadGestiones' => abs( $cantidadGestiones ),
            'gestionInicial' => $matriculaUltimaGestionComprada->id_periodo.'/'.$matriculaUltimaGestionComprada->id_gestion,
            'gestionFinal' => $gestionVigente->id_periodo.'/'.$gestionVigente->id_gestion
        ];

        return response()->json( [
            'data'    => $resultadoCalculo,
            'message' => 'CÁLCULO REALIZADO CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addPeriodo(Request $request){

        $nuevoIdPeriodo = $request->input( 'periodo' );
        $nuevoIdGestion = $request->input( 'gestion' );
        $nuevoEstado    = $request->input( 'estado' );

        // Buscar si ya existe el periodo y gestion en la tabla
        $resp = DB::table( 'periodo_gestion' )
                ->select( '*' )
                ->where( 'id_periodo', '=', $nuevoIdPeriodo )
                ->where( 'id_gestion', '=', $nuevoIdGestion )
                ->get();

        if ( $resp->isEmpty() ) {
            // NO existe la data que quiere introducir, => lo insertamos

            $arrayDatos = [
                'id_periodo' => $nuevoIdPeriodo,
                'id_gestion' => $nuevoIdGestion,
                'estado'     => $nuevoEstado
                // 'fecha_modificacion' => date('Y-m-d H:i:s')
            ];

            $nuevoPeriodoGestion = PeriodoGestion::create( $arrayDatos );

            if ( $nuevoEstado ) {
                // SI es true, => desactiva las demas gesiontes.
                $this->habilitarSoloUnPeriodoGestion( $nuevoPeriodoGestion->id_periodo_gestion, $nuevoEstado);
            }

        }else{
            // Los datos YA EXISTEN => mostramos un mensaje de error.
            return response()->json( [
                'data'    => null,
                'message' => 'LOS DATOS INTRODUCIDOS YA EXISTEN EN LA BASE DE DATOS',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        }

        return response()->json( [
            'data'    => null,
            'message' => 'SE INSERTÓ EL REGISTRO CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function updatePeriodoToActivo(Request $request){

        //** SOLO UN PERIODO PUEDE ESTAR HABILITADO **/

        $idPeriodoGestion = $request->input( 'idPeriodoGestion' );
        $idPeriodo        = $request->input( 'periodo' );
        $idGestion        = $request->input( 'gestion' );
        $nuevoEstado      = $request->input( 'estado' );

        $periodoGestion             = PeriodoGestion::find( $idPeriodoGestion );
        $periodoGestion->id_periodo = $idPeriodo;
        $periodoGestion->id_gestion = $idGestion;
        $periodoGestion->estado     = $nuevoEstado;
        $periodoGestion->save();

        if ( $nuevoEstado ) {
            // SI es true, => desactiva las demas gesiontes.
            $this->habilitarSoloUnPeriodoGestion( $idPeriodoGestion, $nuevoEstado);
        }

       // Buscar si ya existe el periodo y gestion en la tabla
      /*  $resp = DB::table( 'periodo_gestion' )
                ->select( '*' )
                ->where( 'id_periodo', '=', $idPeriodo )
                ->where( 'id_gestion', '=', $idGestion )
                ->get();

        if ( $resp->isEmpty() ) {
            // NO existe la data que quiere introducir, => lo ACTUALIZAMOS

            $periodoGestion             = PeriodoGestion::find( $idPeriodoGestion );
            $periodoGestion->id_periodo = $idPeriodo;
            $periodoGestion->id_gestion = $idGestion;
            $periodoGestion->estado     = $nuevoEstado;
            $periodoGestion->save();

            if ( $nuevoEstado ) {
                // SI es true, => desactiva las demas gesiontes.
                $this->habilitarSoloUnPeriodoGestion( $idPeriodoGestion, $nuevoEstado);
            }

        }else{
            // Los datos YA EXISTEN => mostramos un mensaje de error.
            return response()->json( [
                'data'    => null,
                'message' => 'LOS DATOS INTRODUCIDOS YA EXISTEN EN LA BASE DE DATOS',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        } */

        return response()->json( [
            'data'    => null,
            'message' => 'SE HABILITÓ EL PERIODO SELECCIONADO CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }



    private function habilitarSoloUnPeriodoGestion( $idPeriodoGestionToHabilitar, $nuevoEstado ){
        //** SOLO UN PERIODO PUEDE ESTAR HABILITADO **/

        // 1.- Recupera todos los periodos, sin importar si estan vigentes o no.
        $listaPeriodos =  PeriodoGestion::all();

        // 2. Actualizar el estado del perido seleccionado
        $listaPeriodos->map( function ( $item ) use ( $idPeriodoGestionToHabilitar, $nuevoEstado ) {

            if( $item->id_periodo_gestion == $idPeriodoGestionToHabilitar ){
                $item->estado = true;
                $item->fecha_modificacion = date('Y-m-d H:i:s');
            }else{
                $item->estado = false;
            }
            $item->save();
        });

        return null;
    }

}
