<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Facultad;
use App\Models\Universidad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UniversidadController extends Controller
{
    public function addUniversidad(Request $request){
        $univCreada = Universidad::create( $request->all() );

        return response()->json( [
            'data'    => $univCreada,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );

    }

    public function addFacultad( Request $request ){

        $universidad = Universidad::find( $request->input('id_universidad') );
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

    public function getListUniversidad(){
        $listaUniversidades = Universidad::all();

        return response()->json( [
            'data'    => $listaUniversidades,
            'message' => 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );

    }
}
