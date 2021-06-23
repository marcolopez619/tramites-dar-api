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

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.paterno',
            'usuario.materno',
            'usuario.nombres',
            'usuario.nick_name',
            'usuario.celular',
            'usuario.estado',
            'usuario.id_estudiante as idEstudiante',
            'usuario.id_universidad as idUniversidad',

            'usuario_perfil.id_carrera as idCarrera',
            'carrera.nombre as Carrera'
        ];

        $datosUsuario = DB::table( 'usuario' )
            ->select( $selectColumns )
            ->join( 'usuario_perfil','usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
            ->join( 'carrera', 'carrera.id_carrera' , '=', 'usuario_perfil.id_carrera' )
            ->where( 'usuario.nick_name', '=', $userName )
            ->where( 'usuario.password', '=', $password )
            ->get();

        if ( $datosUsuario->isEmpty() ) {
            return response()->json( [
                'data'    => null,
                'message' => 'USUARIO O CONTRASEÑA INCORRECTA',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST);
        }


        $selectColumns = [
            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil',

            'modulo.id_modulo as idModulo',
            'modulo.nombre as nombreModulo',
            'modulo.icono',

            'recurso.id_recurso as idRecurso',
            'recurso.ruta'
        ];

        $listaRecursos = DB::table( 'usuario' )
        ->join( 'usuario_perfil', 'usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
        ->join( 'perfil', 'perfil.id_perfil' ,'=', 'usuario_perfil.id_perfil' )
        ->join( 'perfil_modulo', 'perfil_modulo.id_perfil' ,'=', 'perfil.id_perfil' )
        ->join( 'modulo', 'modulo.id_modulo' ,'=', 'perfil_modulo.id_modulo' )
        ->join( 'recurso', 'recurso.id_modulo', '=', 'modulo.id_modulo' )
        ->select( $selectColumns )
        ->where( 'usuario.nick_name', '=', $userName )
        ->where( 'usuario.password', '=', $password )
        ->orderBy( 'modulo.nombre', 'ASC' )
        ->get();

        // Une la data del usuario con la lista de recursos //
        $datosUsuario[ 0 ]->recursos = $listaRecursos;

        return response()->json( [
            'data'    => $datosUsuario,
            'message' => 'SESION INICIADA CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }
}
