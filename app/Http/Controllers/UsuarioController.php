<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use App\Models\UsuarioPerfil;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function addUsuario(Request $request){

        $idPerfil     = $request->input( 'idPerfil' );
        $idCarrera    = $request->input( 'idCarrera' );

        $nuevoUsuario                = new usuario();
        $nuevoUsuario->nombre        = $request->input( 'nombre' );
        $nuevoUsuario->password      = $request->input( 'password' );      // TODO: faltaria encriptar el password
        $nuevoUsuario->celular       = $request->input( 'celular' );
        $nuevoUsuario->estado        = $request->input( 'estado' );
        $nuevoUsuario->id_estudiante = $request->input( 'idEstudiante' );
        // $nuevoUsuario->id_universidad = 1 ; // FIXME: Dato quemado, que hace referencia a la UATF
        $nuevoUsuario->save();

        $nuevoUsuario->perfil()->attach( $idPerfil, [ 'id_carrera' => $idCarrera] );

        return response()->json( [
            'data'    => $nuevoUsuario,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function getListaUsuarios(){

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.password as password',
            'usuario.nombre',
            'usuario.celular',
            'usuario.estado',
            'usuario.id_estudiante as idEstudiante',

            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil',

            DB::raw( '(Select id_carrera as idCarrera from carrera where carrera.id_carrera = usuario_perfil.id_carrera)' ),
            DB::raw( '(Select nombre as carrera from carrera where carrera.id_carrera = usuario_perfil.id_carrera)' )

        ];

        $listaUsuarios = DB::table( 'usuario' )
        ->join( 'usuario_perfil', 'usuario_perfil.id_usuario' ,'=', 'usuario.id_usuario' )
        ->join( 'perfil', 'perfil.id_perfil' ,'=', 'usuario_perfil.id_perfil' )
        ->select( $selectColumns )
        // ->where( 'usuario.estado', '=', 1 )
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

    public function updateUsuario(Request $request){

        $idNuevoPerfil = $request->input( 'idPerfil' );

        $usuario           = Usuario::find( $request->input( 'idUsuario' ) );
        $usuario->nombre   = $request->input( 'nombre' );
        $usuario->password = $request->input( 'password' ); // TODO: faltaria encriptar el password
        $usuario->celular  = $request->input( 'celular' );
        $usuario->estado   = $request->input( 'estado' );
        $usuario->save();

        $usuarioPerfil = UsuarioPerfil::find( $usuario->id_usuario );
        $usuarioPerfil->id_perfil = $idNuevoPerfil;
        $usuarioPerfil->save();

        return response()->json( [
            'data'    => $usuarioPerfil,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );

    }

}
