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

    public function updatePeriodoToActivo(Request $request){

        //** SOLO UN PERIODO PUEDE ESTAR HABILITADO **/

        $idPeriodoGestion = $request->input( 'idPeriodoGestion' );
        $nuevoEstado = $request->input( 'estado' );

        //** TODO: OJO, FALTA VALIDAR SI POR EJEMPLO, se debe poder desabilitar la gestion, pero teniendo en cuenta q existen tramites habilitados de esa gestion.**//

        // 1.- Recupera todos los periodos, sin importar si estan vigentes o no.
        $listaPeriodos =  PeriodoGestion::all();

        // 2. Actualizar el estado del perido seleccionado
        $listaPeriodos->map( function ( $item ) use ( $idPeriodoGestion, $nuevoEstado ) {

            if( $item->id_periodo_gestion == $idPeriodoGestion ){
                $item->estado = true;
                $item->fecha_modificacion = date('Y-m-d H:i:s');
            }else{
                $item->estado = false;
            }
            $item->save();
        });

        // 3.- Retornar respuesta
        return response()->json( [
            'data'    => null,
            'message' => 'SE HABILITÓ EL PERIODO SELECCIONADO CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }

}
