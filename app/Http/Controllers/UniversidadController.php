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
    public function getListaCarreras($idFacultad){

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
        $listaUniversidades = Universidad::all();

        $listaUniversidadesRenamedKeys = array();

        foreach ($listaUniversidades as $item) {
            $newData = [
                'idUniversidad' => $item->id_universidad,
                'nombre' => $item->nombre,
                'estado' => $item->estado
            ];

            array_push( $listaUniversidadesRenamedKeys, $newData );
        }

        return response()->json( [
            'data'    => $listaUniversidadesRenamedKeys,
            'message' => 'SE ENCONTRARON RESULTADOS',
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
        $facultad = Facultad::find( $request->input('idFacultad'));

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
