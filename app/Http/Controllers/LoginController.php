<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function inicializarContexto( Request $request ){

        $userName = $request->input( 'usuario' );
        $password = $request->input( 'password' );

        // TODO: faltaria encriptar el password para la verificacion

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.nombre',
            'usuario.celular',
            'usuario.estado',

            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil',

            'modulo.id_modulo as idModulo',
            'modulo.nombre as nombreModulo',

            'recurso.id_recurso as idRecurso',
            'recurso.ruta',

        ];

        $listaUsuarios = DB::table( 'usuario' )
        ->join( 'usuario_perfil', 'usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
        ->join( 'perfil', 'perfil.id_perfil' ,'=', 'usuario_perfil.id_perfil' )
        ->join( 'perfil_modulo', 'perfil_modulo.id_perfil' ,'=', 'perfil.id_perfil' )
        ->join( 'modulo', 'modulo.id_modulo' ,'=', 'perfil_modulo.id_modulo' )
        ->join( 'recurso', 'recurso.id_modulo', '=', 'modulo.id_modulo' )
        ->select( $selectColumns )
        ->where( 'usuario.nombre', '=', $userName )
        ->where( 'usuario.password', '=', $password )
        ->get();

        return response()->json( [
            'data'    => $listaUsuarios->isEmpty() ? null : $listaUsuarios,
            'message' => $listaUsuarios->isEmpty() ? 'USUARIO O CONTRASEÑA INCORRECTA' : 'SESION INICIADA CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }
}
