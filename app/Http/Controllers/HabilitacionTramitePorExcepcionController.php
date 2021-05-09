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
}
