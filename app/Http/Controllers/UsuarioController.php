<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\EstudianteCarrera;
use App\utils\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use App\Models\UsuarioPerfil;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class UsuarioController extends Controller
{
    public function addUsuario(Request $request){

        $idPerfil     = $request->input( 'idPerfil' );
        $idCarrera    = $request->input( 'idCarrera' );
        $idEstudiante = $request->input( 'idEstudiante' );

        // Verifica si el $idPerfil es Estudiante, si lo es => insertar en las tablas: estudiante, estudiante_carrera y finalmente en la tabla idEstudiante;
        // Caso contrario insertar solo en la tabla Usuario.
        $existeDatosRepetidos = $this->verificarDatosRepetidos($request->input('nickName'));

        if ( $existeDatosRepetidos ) {
            return response()->json( [
                'data'    => null,
                'message' => 'EL NICKNAME YA SE ENCUENTRA ASIGNADO A OTRA PERSONA',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );

            return;
        }


        $nuevoUsuario                 = new usuario();
        $nuevoUsuario->paterno        = $request->input( 'paterno' );
        $nuevoUsuario->materno        = $request->input( 'materno' );
        $nuevoUsuario->nombres        = $request->input( 'nombres' );
        $nuevoUsuario->nick_name      = $request->input( 'nickName' );
        $nuevoUsuario->password       = $request->input( 'password' );
        $nuevoUsuario->celular        = $request->input( 'celular' );
        $nuevoUsuario->estado         = $request->input( 'estado' );
        $nuevoUsuario->fecha_creacion = date("Y-m-d H:m:s",time());
        $nuevoUsuario->id_estudiante  = $idEstudiante;
        $nuevoUsuario->save();

        $nuevoUsuario->perfil()->attach( $idPerfil, [ 'id_carrera' => $idCarrera] );

        return response()->json( [
            'data'    => $nuevoUsuario,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function deleteUsuario(Request $request){

        Usuario::destroy( $request->input('idUsuario'));

        return response()->json( [
            'data'    => null,
            'message' => 'SE ELIMINÓ AL USUARIO CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );

    }

    public function getListaUsuarios(){

        $selectColumns = [
            'usuario.id_usuario as idUsuario',
            'usuario.paterno',
            'usuario.materno',
            'usuario.nombres',
            'usuario.nick_name as nickName',
            'usuario.password as password',
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
        ->orderBy( 'usuario.fecha_creacion', 'DESC' )
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
        $idNuevaCarrera = $request->input( 'idCarrera' );

        $usuario           = Usuario::find( $request->input( 'idUsuario' ) );

        $usuario->paterno   = $request->input( 'paterno' );
        $usuario->materno   = $request->input( 'materno' );
        $usuario->nombres   = $request->input( 'nombres' );
        $usuario->nick_name = $request->input( 'nickName' );
        $usuario->password  = $request->input( 'password' );
        $usuario->celular   = $request->input( 'celular' );
        $usuario->estado    = $request->input( 'estado' );
        $usuario->save();

        $usuarioPerfil = UsuarioPerfil::where( 'id_usuario', '=', $usuario->id_usuario )->first();
        $usuarioPerfil->id_perfil = $idNuevoPerfil;
        $usuarioPerfil->id_carrera = $idNuevaCarrera;
        $usuarioPerfil->save();

        // Verifica si el perfil es de Estudiante
        if ($idNuevoPerfil == Perfil::ESTUDIANTE) {
            // => Actualiza sus datos principales en la tabla estudiante
            $estudiante = Estudiante::find( $usuario->id_estudiante );
            $estudiante->paterno   = $request->input( 'paterno' );
            $estudiante->materno   = $request->input( 'materno' );
            $estudiante->nombres   = $request->input( 'nombres' );
            $estudiante->save();

            // => Actualiza la tabla: Estudiante_carrera, con la nueva carrera del estudiante
            $estudianteCarrera = EstudianteCarrera::where( 'id_estudiante', '=', $estudiante->id_estudiante )->first();
            $estudianteCarrera->id_carrera = $idNuevaCarrera;
            $estudianteCarrera->save();
        }

        return response()->json( [
            'data'    => $usuarioPerfil,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );

    }

    private function verificarDatosRepetidos( $pNickName ){
        $usuario = DB::table( 'usuario'  )
                    ->select()
                    ->where( 'usuario.nick_name', '=', $pNickName )
                    ->get();

        return !$usuario->isEmpty();
    }

}
