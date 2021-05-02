<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    public function addEstudiante(Request $request)
    {
        $carrera = Carrera::find($request->input('idCarrera'));

        $nuevoEstudiante                   = new Estudiante();
        $nuevoEstudiante->ru               = $request->input('ru');
        $nuevoEstudiante->ci               = $request->input('ci');
        $nuevoEstudiante->complemento      = $request->input('complemento');
        $nuevoEstudiante->paterno          = $request->input('paterno');
        $nuevoEstudiante->materno          = $request->input('materno');
        $nuevoEstudiante->nombres          = $request->input('nombres');
        $nuevoEstudiante->fecha_nacimiento = $request->input('fechaNacimiento');
        $nuevoEstudiante->sexo             = $request->input('sexo');
        $nuevoEstudiante->save();

        $idCarrera = array($carrera->id_carrera);

        $nuevoEstudiante->carrera()->attach($idCarrera, ['estado' => 1]);

        return response()->json(
            [
                'data'    => $nuevoEstudiante,
                'message' => 'INSERSION CORRECTA',
                'error'   => null
            ],
            Response::HTTP_CREATED
        );
    }

    public function getInformacionEstudiante($idEstudiante)
    {
        $arrayCamposSelect = [
            'facultad.id_facultad AS idFacultad',
            'facultad.nombre AS facultad',
            'carrera.id_carrera AS idCarrera',
            'carrera.nombre AS carrera',
            'estudiante.id_estudiante as idEstudiante',
            'estudiante.ru',
            'estudiante.ci',
            'estudiante.complemento',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.nombres',
             // DB::raw("estudiante.paterno || ' ' || estudiante.materno || ' ' || estudiante.nombres AS nombreCompleto" ),
            'estudiante.fecha_nacimiento AS fechaNacimiento',
            'estudiante.sexo'
        ];

        $estudiante = DB::table('estudiante')
            ->join('estudiante_carrera', 'estudiante.id_estudiante', '=', 'estudiante_carrera.id_estudiante')
            ->join('carrera', 'estudiante_carrera.id_carrera', '=', 'carrera.id_carrera')
            ->join('facultad', 'carrera.id_facultad', '=', 'facultad.id_facultad')
            ->select( $arrayCamposSelect )
            ->where('estudiante.id_estudiante', '=', $idEstudiante)
            ->get();

            if ( !$estudiante->isEmpty() ) {
                $estudiante[ 0 ]->nombreCompleto = $estudiante[ 0 ]->paterno.' '.$estudiante[ 0 ]->materno.' '.$estudiante[ 0 ]->nombres;
            }

        return response()->json([
            'data'    => $estudiante->isEmpty() ? null : $estudiante[ 0 ],
            'message' => $estudiante->isEmpty() ? 'NO SE ENCONTRARON RESULTADOS' : 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ]);
    }
}
