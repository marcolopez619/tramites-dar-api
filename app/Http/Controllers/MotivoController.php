<?php

namespace App\Http\Controllers;

use App\Models\Motivo;
use Illuminate\Http\Request;

class MotivoController extends Controller
{
    public function getListaMotivo(){

        $selectColumns = [
            'motivo.id_motivo as idMotivo',
            'motivo.descripcion as descripcionMotivo'
        ];

        $listaMotivos = Motivo::all( $selectColumns )->where('idMotivo', '>', 0);

        return response()->json([
            'data'    => $listaMotivos->isEmpty() ? null : $listaMotivos,
            'message' => $listaMotivos->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }
}
