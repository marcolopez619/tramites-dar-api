<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Facultad;
use App\Models\Universidad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UniversidadController extends Controller
{
    /** Retorna todas las carreras disponibles pertenecientes a la UATF
     * getListaCarreras
     *
     * @return listaCarreras
     */
    public function getListaCarreras(){

        $selectColumns = [
            'carrera.id_carrera as idCarrera',
            'carrera.nombre',
            'carrera.estado',
            'carrera.id_facultad as idFacultad'
        ];

        $listaCarreras = DB::table( 'carrera' )
        ->join( 'facultad', 'facultad.id_facultad', '=', 'carrera.id_facultad' )
        ->join( 'universidad', 'universidad.id_universidad', '=', 'facultad.id_universidad' )
        ->select( $selectColumns )
        ->where( 'universidad.id_universidad', '=', 2 ) // 2 = TOMAS FRIAS
        ->where( 'carrera.estado', '=', 1 ) // Carreras activas
        ->orderBy( 'carrera.nombre', 'ASC' )
        ->get();

        return response()->json( [
            'data'    => $listaCarreras->isEmpty() ? null : $listaCarreras,
            'message' => $listaCarreras->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getListaCarrerasTransferencia($nombreCarrera){

        $selectColumns = [
            'carrera.id_carrera as idCarrera',
            'carrera.nombre',
            'carrera.estado',
            'carrera.id_facultad as idFacultad'
        ];

        $listaCarreras = DB::table( 'carrera' )
        ->join( 'facultad', 'facultad.id_facultad', '=', 'carrera.id_facultad' )
        ->join( 'universidad', 'universidad.id_universidad', '=', 'facultad.id_universidad' )
        ->select( $selectColumns )
        ->where( 'universidad.id_universidad', '=', 2 ) // 2 = TOMAS FRIAS
        ->where( 'carrera.estado', '=', 1 ) // Carreras activas
        ->where( 'carrera.nombre', 'LIKE', '%'.strtoupper($nombreCarrera).'%' )
        ->orderBy( 'carrera.nombre', 'ASC' )
        ->get();

        // $products = Product::where('name_en', 'LIKE', '%'.$search.'%')->get();

        return response()->json( [
            'data'    => $listaCarreras->isEmpty() ? null : $listaCarreras,
            'message' => $listaCarreras->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getListaCarrerasByIdFacultad($idFacultad){

        $selectColumns = [
            'carrera.id_carrera as idCarrera',
            'carrera.nombre',
            'carrera.estado',
            'carrera.id_facultad as idFacultad'
        ];

        $listaCarreras = DB::table( 'carrera' )
        ->select( $selectColumns )
        ->where( 'carrera.id_facultad', '=', $idFacultad )
        ->orderBy( 'carrera.nombre', 'ASC' )
        ->get();

        return response()->json( [
            'data'    => $listaCarreras->isEmpty() ? null : $listaCarreras,
            'message' => $listaCarreras->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function getListUniversidad(){
        $selectColumns = [
            'id_universidad as idUniversidad',
            'nombre',
            'sigla',
            'estado'
        ];

        $listaUniversidades = Universidad::all( $selectColumns );

        return response()->json( [
            'data'    => $listaUniversidades,
            'message' => 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );

    }

    public function getListaCarrerasByIdUniversidad($idUniversidad){

        $selectColumns = [
            'universidad.id_universidad as idUniversidad',
            'facultad.id_facultad as idFacultad',
            'facultad.nombre as facultad',
            'facultad.estado as estadoFacultad',
            'carrera.id_carrera as idCarrera',
            'carrera.nombre as carrera',
            'carrera.estado as estadoCarrera'
        ];

        $listaCarreras = DB::table( 'universidad' )
        ->join( 'facultad', 'facultad.id_universidad', '=' , 'universidad.id_universidad' )
        ->join( 'carrera', 'carrera.id_facultad', '=' , 'facultad.id_facultad' )
        ->select( $selectColumns )
        ->where( 'universidad.id_universidad', '=', $idUniversidad )
        ->orderBy( 'carrera.nombre', 'ASC' )
        ->get();

        return response()->json( [
            'data'    => $listaCarreras->isEmpty() ? null : $listaCarreras,
            'message' => $listaCarreras->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }


    public function addUniversidad(Request $request){
        $univCreada = Universidad::create( $request->all() );

        return response()->json( [
            'data'    => $univCreada,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function updateUniversidad(Request $request ){
        $universidad = Universidad::find( $request->input( 'idUniversidad' ));
        $universidad->nombre = $request->input( 'nombre' );
        $universidad->estado = $request->input( 'estado' );
        $universidad->save();

        return response()->json( [
            'data'    => $universidad,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addFacultad( Request $request ){

        $universidad = Universidad::find( $request->input('idUniversidad') );
        $facultad = new Facultad();
        $facultad->nombre = $request->input('nombre');
        $facultad->estado = $request->input('estado');
        $facultadCreada = $universidad->facultad()->save( $facultad );

        return response()->json( [
            'data'    => $facultadCreada,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }

    public function getListaFacultades($idUniversidad){

        $selectColumns = [
            'facultad.id_facultad AS idFacultad',
            'facultad.nombre',
            'facultad.estado',
            'facultad.id_universidad AS idUniversidad'
        ];

        $listaFacultades = DB::table('facultad')
            ->select($selectColumns)
            ->where( 'facultad.id_universidad', '=' , $idUniversidad )
            ->orderBy('facultad.nombre', 'ASC')
            ->get();

        return response()->json([
            'data'    => $listaFacultades->isEmpty() ? null : $listaFacultades,
            'message' => $listaFacultades->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK);

    }

    public function updateFacultad(Request $request ){
        $facultad = Facultad::find( $request->input( 'idFacultad' ) );
        $facultad->nombre = $request->input( 'nombre' );
        $facultad->estado = $request->input( 'estado' );
        $facultad->save();

        return response()->json( [
            'data'    => $facultad,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );
    }

    public function addCarrera( Request $request ){
        // Verificar si una carrera existe dos veces
        $facultad = Facultad::find( $request->input('idFacultad'));
        $nombreNuevaCarrera = strtoupper( trim( $request->input('nombre') ) );

        // Verifica si ya existe la carrera nueva a crear
        $universidad = Universidad::find( $facultad->id_universidad );

        $selectColumns = [
            DB::raw("(select distinct count( * ) from carrera c where c.nombre = '".$nombreNuevaCarrera. "' and c.id_facultad in ( select id_facultad from facultad f where f.id_universidad = $universidad->id_universidad ) )")
        ];

        $resp = DB::table('carrera')
            ->select( $selectColumns )
            ->get();

        if ( $resp[ 0 ]->count >= 1 ) {
            // El nombre de la carrera es repetida, => mostramos un mensaje al usuario
            return response()->json( [
                'data'    => null,
                'message' => "LA CARRERA: '$nombreNuevaCarrera' YA SE ENCUENTRA REGISTRADA",
                'error'   => null
            ], Response::HTTP_BAD_REQUEST );
        }



        $carrera = new Carrera();
        $carrera->nombre = $request->input('nombre');
        $carrera->estado = $request->input('estado');
        $carreraCreada = $facultad->carrera()->save( $carrera );


        return response()->json( [
            'data'    => $carreraCreada,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }

    public function updateCarrera( Request $request ){
        $carrera = Carrera::find( $request->input( 'idCarrera' ));
        $carrera->nombre = $request->input( 'nombre' );
        $carrera->estado = $request->input( 'estado' );
        $carrera->save();

        return response()->json( [
            'data'    => $carrera,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );
    }



    public function getAllInformation($idUniversidad){

        $selectColumns = [
            "universidad.id_universidad AS idUniversidad",
            "universidad.nombre AS universidad",
            "universidad.estado AS estadoUniversidad",

            "facultad.id_facultad AS idFacultad",
            "facultad.nombre AS facultad",
            "facultad.estado AS estadofacultad",


            "carrera.id_carrera AS idCarrera",
            "carrera.nombre AS carrera",
            "carrera.estado AS estadoCarrera",
        ];

        $Data = DB::table('universidad')
            ->join( 'facultad', 'facultad.id_universidad', '=' , 'universidad.id_universidad' )
            ->join( 'carrera' , 'carrera.id_facultad' , '=' , 'facultad.id_facultad' )
            ->select( $selectColumns )
            ->where( 'universidad.id_universidad', '=' , $idUniversidad )
            ->orderBy( 'carrera.nombre' , 'ASC' )
            ->get();

        return response()->json( [
            'data'    => $Data->isEmpty() ? null : $Data,
            'message' => $Data->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );

    }
}
