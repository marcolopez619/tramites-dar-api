<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstudianteController extends Controller
{
    public function addEstudiante(Request $request)
    {
        $carrera = Carrera::find( $request->input( 'idCarrera' ));

        $nuevoEstudiante                   = new Estudiante();
        $nuevoEstudiante->ru               = $request->input( 'ru' );
        $nuevoEstudiante->ci               = $request->input( 'ci' );
        $nuevoEstudiante->complemento      = $request->input( 'complemento' );
        $nuevoEstudiante->paterno          = $request->input( 'paterno' );
        $nuevoEstudiante->materno          = $request->input( 'materno' );
        $nuevoEstudiante->nombres          = $request->input( 'nombres' );
        $nuevoEstudiante->fecha_nacimiento = $request->input( 'fechaNacimiento' );
        $nuevoEstudiante->sexo             = $request->input( 'sexo' );
        $nuevoEstudiante->save();

        $idCarrera = array( $carrera->id_carrera );

        $nuevoEstudiante->carrera()->attach( $idCarrera , [ 'estado' => 1 ] );

        return response()->json([
            'data'    => $nuevoEstudiante,
            'message' => 'INSERSION CORRECTA',
            'error'   => null],
            Response::HTTP_CREATED );
    }
}
