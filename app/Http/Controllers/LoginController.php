<?php

namespace App\Http\Controllers;

use App\utils\Estado;
use App\Models\Perfil;
use App\Models\usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EstudianteTramite;
use App\Models\Tramite;
use App\utils\Entidad;
use App\utils\Tipotramite;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    public function inicializarContexto( Request $request ){

        $userName = $request->input( 'usuario' );
        $password = $request->input( 'password' );
        $tramiteEnCurso = null;
        // $tramitesPermitidos = [ Tipotramite::SUSPENCION, Tipotramite::READMISION ];

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
                 $tramiteEnCurso = $this->verificarExistenciaTramiteConcluido( $datosUsuario->first()->idEstudiante );

                 // $esTramiteSuspencionReadmision = in_array($tramite->id_tramite, $tramitesPermitidos );

                 /* $existeTramiteAprobadorPorDAR = empty($tramiteEnCurso);


                 if ( !$existeTramiteAprobadorPorDAR ) // && !$esTramiteSuspencionReadmision) {
                     {
                        return response()->json( [
                            'data'    => null,
                            'message' => 'USUARIO DESABILITADO POR HABER CONCLUIDO EL TRAMITE DE : '.$tramiteEnCurso->descripcion,
                            'error'   => null
                        ], Response::HTTP_OK);
                    } */


            }

        }




        $selectColumns = [
            'perfil.id_perfil as idPerfil',
            'perfil.nombre as nombrePerfil',

            'modulo.id_modulo as idModulo',
            'modulo.nombre as nombreModulo',
            'modulo.icono',
            'modulo.visible',

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

            $tramitesEnCursoFinalizado = $this->getTramitesEnCursoOrFinalizados( $datosUsuario->first()->idEstudiante );

            if ( !$tramitesEnCursoFinalizado->isEmpty() ) {
                // => filtra sus recursos, a solo aquel que que esta en curso
                $array = [ $tramitesEnCursoFinalizado->first()->id_tramite ];

                if( $tramitesEnCursoFinalizado->first()->id_tramite == Tipotramite::SUSPENCION){
                    array_push( $array, Tipotramite::READMISION );
                }

                $arrayRecursos = [];

                foreach ($listaRecursos as $item) {
                    // if ( $item->idModulo ==  $tramitesEnCursoFinalizado->first()->id_tramite ) {
                    if ( in_array( $item->idModulo, $array ) ) {
                        array_push( $arrayRecursos, $item );
                    }
                }

                $listaRecursos = $arrayRecursos;

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
        $estado = [ Estado::APROBADO, Estado::FINALIZADO ];

        $tramite = null;

        $estudianteTramite = DB::table('estudiante_tramite')
                                ->where( 'estudiante_tramite.id_estudiante', '=', $idEstudiante)
                                ->whereIn( 'estudiante_tramite.id_estado', $estado )
                                ->whereIn( 'estudiante_tramite.id_entidad', $entidad )
                                ->get();

        if ( !$estudianteTramite->isEmpty() ) {
            $estudianteTramite =  $estudianteTramite->first();

            $tramite = Tramite::find( $estudianteTramite->id_tramite );
        }




        return $tramite;
        // return $estudianteTramite->isEmpty();
    }



    private function getTramitesEnCursoOrFinalizados($idEstudiante){

        $estados = [ Estado::ENVIADO, Estado::APROBADO, Estado::FINALIZADO ];

        $estudianteTramite = DB::table('estudiante_tramite')
                                ->where( 'estudiante_tramite.id_estudiante', '=', $idEstudiante)
                                ->whereIn( 'estudiante_tramite.id_estado', $estados )
                                ->orderByDesc( 'estudiante_tramite.fecha_proceso' )
                                ->get();

        return $estudianteTramite;
    }
}
