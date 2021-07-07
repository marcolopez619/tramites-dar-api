<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\Estudiante;
use App\Models\PeriodoGestion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\utils\Tipotramite;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function getCantidadPorTipoTramite( $idGestion ){

        $data = DB::select( "select * from public.p_reporte_cantidad_estudiantes_por_tramite( $idGestion );" );

        return response()->json([
            'data'    => empty( $data ) ? null : $data,
            'message' => empty( $data ) ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }

    public function getCantidadPorTipoTramitePorCarrera( $idGestion, $idCarrera ){

        $data = DB::select( "select * from public.p_reporte_cantidad_estudiantes_por_tramite_por_carrera( $idGestion, $idCarrera );" );

        return response()->json([
            'data'    => empty( $data ) ? null : $data,
            'message' => empty( $data ) ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }


}
