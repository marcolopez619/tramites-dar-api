<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listaEntidades  = Entidad::all();

        return response()->json( [
            'data'    => $listaEntidades,
            'message' => 'SE ENCONTRARON RESULTADOS',
            'error'   => null
        ], Response::HTTP_OK );
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nuevaEntidad = Entidad::create( $request->all() );

        return response()->json( [
            'data'    => $nuevaEntidad,
            'message' => 'INSERCION CORRECTA',
            'error'   => null
        ], Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Entidad  $entidad
     * @return \Illuminate\Http\Response
     */
    public function show(Entidad $entidad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entidad  $entidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entidad $entidad)
    {
        $entidad = Entidad::find( $request->input( 'idEntidad' ));
        $entidad->descripcion = $request->input( 'descripcion' );
        $entidad->save();

        return response()->json( [
            'data'    => $entidad,
            'message' => 'ACTUALIZACION CORRECTA',
            'error'   => null
        ], Response::HTTP_OK );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Entidad  $entidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entidad $entidad)
    {
        //
    }
}
