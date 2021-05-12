<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function addUsuario(Request $request){

        $idPerfil = $request->input( 'idPerfil' );

        $nuevoUsuario                 = new usuario();
        $nuevoUsuario->nombre         = $request->input( 'nombre' );
        $nuevoUsuario->password       = $request->input( 'password' );  // TODO: faltaria encriptar el password
        $nuevoUsuario->celular        = $request->input( 'celular' );
        $nuevoUsuario->estado         = $request->input( 'estado' );
        $nuevoUsuario->id_universidad = 1 ; // FIXME: Dato quemado, que hace referencia a la UATF
        $nuevoUsuario->save();

        $nuevoUsuario->perfil()->attach( $idPerfil );

        return response()->json( [
            'data'    => $nuevoUsuario,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function getListaUsuarios(){

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.nombre',
            'usuario.celular',
            'usuario.estado',

            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil'

        ];

        $listaUsuarios = DB::table( 'usuario' )
        ->join( 'usuario_perfil', 'usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
        ->join( 'perfil', 'perfil.id_perfil' ,'=', 'usuario_perfil.id_perfil' )
        ->select( $selectColumns )
        ->where( 'usuario.estado', '=', 1 ) // FIXME: Dato quemado par mostrar los usuario activos
        ->orderBy( 'usuario.nombre', 'ASC' )
        ->get();

        return response()->json( [
            'data'    => $listaUsuarios->isEmpty() ? null : $listaUsuarios,
            'message' => $listaUsuarios->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getusuario($idUsuario){

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.nombre',
            'usuario.celular',
            'usuario.estado',

            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil'
        ];

        $listaUsuarios = DB::table( 'usuario' )
        ->join( 'usuario_perfil', 'usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
        ->join( 'perfil', 'perfil.id_perfil' ,'=', 'usuario_perfil.id_perfil' )
        ->select( $selectColumns )
        ->where( 'usuario.id_usuario', '=', $idUsuario )
        ->get();

        return response()->json( [
            'data'    => $listaUsuarios->isEmpty() ? null : $listaUsuarios,
            'message' => $listaUsuarios->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

}
