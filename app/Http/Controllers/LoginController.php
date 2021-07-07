<?php

namespace App\Http\Controllers;

use App\utils\Estado;
use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use App\utils\Entidad;
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

        } else {

            // Verifica si ya le aprobaron (Por el DAR) algun tramite que realizó anteriormente para no dejarle ingresar al sistema
            $idEstudiante = $datosUsuario->first()->idEstudiante;

            if ( $idEstudiante > 0 ) {
                $existeTramiteAprobadorPorDAR = $this->verificarExistenciaTramiteConcluido( $datosUsuario->first()->idEstudiante );

                if ( !$existeTramiteAprobadorPorDAR) {

                    return response()->json( [
                        'data'    => null,
                        'message' => 'USUARIO DESABILITADO POR HABER CONCLUIDO UN TRAMITE POR EL D.A.R.',
                        'error'   => null
                    ], Response::HTTP_OK);

                }
            }
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

        // Verifica si es un estudiante el que inicia sesion para mostrar los menus permitidos en caso de estar haciendo un tramite.

        // Si es un estudiante, => su idEstudiante > 0,
        if ( $datosUsuario->first()->idEstudiante > 0 ) {

            $tramitesEnCurso = $this->getTramitesEnCurso( $datosUsuario->first()->idEstudiante );

            if ( !$tramitesEnCurso->isEmpty() ) {
                // => filtra sus recursos, a solo aquel que que esta en curso
                $listaRecursos = $listaRecursos->whereIn( 'idModulo', [ $tramitesEnCurso->first()->id_tramite] );
            }
        }

        // Une la data del usuario con la lista de recursos //
        $datosUsuario[ 0 ]->recursos = $listaRecursos;

        return response()->json( [
            'data'    => $datosUsuario,
            'message' => 'SESION INICIADA CORRECTAMENTE',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function changePassword( Request $request ){

        $idUsuario = $request->input( 'idUsuario' );
        $nuevoPassword = $request->input( 'nuevoPassword' );

        $usuario = usuario::find( $idUsuario );

        if ( !empty($usuario ) ) {

            $usuario->password = $nuevoPassword;
            $usuario->save();

            return response()->json( [
                'data'    => $usuario,
                'message' => 'SE CAMBIO SU PASSWORD CORRECTAMENTE, INICIE SESION NUEVAMENTE',
                'error'   => null
            ], Response::HTTP_OK );
        }else {

            return response()->json( [
                'data'    => null,
                'message' => 'NO SE ENCONTRÓ AL USUARIO, POR FAVOR VERIFIQUE',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        }


    }



    private function verificarExistenciaTramiteConcluido($idEstudiante){

        $entidad = [ Entidad::ENCARGADO_DAR ];
        $estado = [ Estado::APROBADO ];

        $estudianteTramite = DB::table('estudiante_tramite')
                                ->where( 'estudiante_tramite.id_estudiante', '=', $idEstudiante)
                                ->whereIn( 'estudiante_tramite.id_estado', $estado )
                                ->whereIn( 'estudiante_tramite.id_entidad', $entidad )
                                ->get();

        return $estudianteTramite->isEmpty();
    }
}
