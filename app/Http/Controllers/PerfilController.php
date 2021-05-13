<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function getListaPerfiles(){

        $selectColumns = [
            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil'
        ];

        $listaPerfiles = DB::table( 'perfil' )
        ->select( $selectColumns )
        ->orderBy( 'perfil.nombre', 'ASC' )
        ->get();

        return response()->json( [
            'data'    => $listaPerfiles->isEmpty() ? null : $listaPerfiles,
            'message' => $listaPerfiles->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

}
