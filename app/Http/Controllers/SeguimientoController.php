<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\Estudiante;
use App\utils\Tipotramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;
use App\Models\EstudianteTramiteHistorico;
use App\Models\Tramite;

class SeguimientoController extends Controller
{
   public function getSeguimientoTramite($idTipoTramite, $idTramite)
   {
       $respfromDBFunction = $this->getDataFromDBFunction($idTipoTramite, $idTramite);

        return response()->json([
            'data'    => empty( $respfromDBFunction ) ? null : $respfromDBFunction,
            'message' => empty( $respfromDBFunction ) ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
   }


   private function getDataFromDBFunction( $idTipoTramite,$idTramite ){

    $resp = null;

    switch ($idTipoTramite) {
        case Tipotramite::ANULACION : $resp = DB::select( "select * from public.p_seguimiento_anulacion( $idTramite );" );  break;
        case Tipotramite::TRASPASO_UNIVERSIDAD : $resp = DB::select( "select * from public.p_seguimiento_traspaso( $idTramite );" );  break;
        default: break;
    }

    return $resp;
   }


}
