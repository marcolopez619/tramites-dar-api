<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use eTipoTramite;
use Illuminate\Http\Request;
use App\Models\transferencia;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use Illuminate\Support\Facades\DB;
use TipoTramite;

class DarController extends Controller
{
    public function getTramitesPorAtender(){
        $selectColumn = [
            'estudiante.id_estudiante AS idEstudiante',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
            'carrera.nombre AS carrera',
            'estudiante_tramite.id_estudiante_tramite as idEstudianteTramite',
            'estudiante_tramite.fecha AS fechaSolicitud',
            'estudiante_tramite.id_estado AS estado',
            'estudiante_tramite.id_tipo_tramite AS idTipoTramite',
            'estudiante_tramite.id_tramite AS idTramite',
            'tramite.descripcion AS tipoTramite'
        ];



        $listaSolicitudes = DB::table('carrera')
            ->join('estudiante_carrera', 'carrera.id_carrera', '=' , 'estudiante_carrera.id_carrera')
            ->join('estudiante', 'estudiante_carrera.id_estudiante', '=' , 'estudiante.id_estudiante')
            ->join( 'estudiante_tramite', 'estudiante.id_estudiante', '=', 'estudiante_tramite.id_estudiante' )
            ->join( 'tramite', 'estudiante_tramite.id_tramite', '=', 'tramite.id_tramite')

            ->select( $selectColumn )
            ->where( 'estudiante_tramite.id_entidad', '=' , 1 ) // FIXME: el 1, debe ser de la entidad, en este caso del encargado del DAR = 2
            ->distinct()
            ->orderBy( 'estudiante_tramite.fecha' , 'DESC')
            ->get();

        return response()->json([
            'data'    => $listaSolicitudes->isEmpty() ? null : $listaSolicitudes,
            'message' => $listaSolicitudes->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }

   /*  public function getDetalleTramite(Request $request){
        $idTramite = $request->input('idTramite');
        $idEstudiante = $request->input('idEstudiante');

        switch ($idTramite) {
            case  TipoTramite::ANULACION :
                DB::raw( 'anulacion' )
                ->
                break;

            default:
                # code...
                break;
        }

    } */
}
