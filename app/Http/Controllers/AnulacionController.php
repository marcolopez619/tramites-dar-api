<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use App\Models\EstudianteTramite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnulacionController extends Controller
{
    public function getListaAnulacion()
    {
    }

    public function addAnulacion(Request $request)
    {

        $arrayDataAnulacion = [
            'fecha_solicitud' => date('Y-m-d H:i:s'),
            'motivo'          => $request->input('motivo'),
            'id_estudiante'   => $request->input('idEstudiante'),
        ];

        // Retorna un booleano como respuesta de insercion
        $nuevaAnulacion = Anulacion::insert($arrayDataAnulacion);

        $dataEstudianteTramite = [
            'id_estudiante' => $request->input('idEstudiante'),
            'id_tramite'    => $request->input('idTramite'),
            'id_estado'     => $request->input('idEstado'),
            'id_entidad'    => $request->input('idEntidad'),
            'fecha'         => date('Y-m-d H:i:s'),
            'observaciones' => $request->input('observaciones')
        ];

        // Retorna un booleano como respuesta de insercion
        $estudianteTramite = EstudianteTramite::insert($dataEstudianteTramite);

        return response()->json([
            'data'    => [
                'Anulacion'         => $nuevaAnulacion,
                'EstudianteTramite' => $estudianteTramite
            ],
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED);
    }
}
