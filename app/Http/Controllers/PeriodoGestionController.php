<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PeriodoGestion;

class PeriodoGestionController extends Controller
{
    public function getPeriodoActivo(){
        $selectColumns = ['id_periodo_gestion as idPeriodoGestion' , 'id_periodo as periodo', 'id_gestion as gestion', 'estado'];

        $data = PeriodoGestion::select( $selectColumns )->where( 'estado' , '=', true )->get();

        return response()->json( [
            'data'    => $data->isEmpty() ? null :  $data[ 0 ],
            'message' => $data->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getAllPeriodos(){
        $selectColumns = ['id_periodo_gestion as idPeriodoGestion' , 'id_periodo as periodo', 'id_gestion as gestion', 'estado'];

        $data = PeriodoGestion::select( $selectColumns )->get();

        return response()->json( [
            'data'    => $data->isEmpty() ? null :  $data,
            'message' => $data->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADO',
            'error'   => null
        ], Response::HTTP_OK );
    }
}
