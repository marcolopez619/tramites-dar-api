<?php

use App\Http\Controllers\AnulacionController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\EstudianteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\HabilitacionTramiteController;
use App\Http\Controllers\TramiteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//** UNIVERSIDAD **/
Route::get( '/universidad' , [UniversidadController::class, 'getListUniversidad' ]);
Route::post( '/universidad' , [UniversidadController::class, 'addUniversidad' ]);
Route::patch( '/universidad' , [UniversidadController::class, 'updateUniversidad' ]);

//** FACULTADES **/
Route::post( '/facultad' , [UniversidadController::class, 'addFacultad' ]);
Route::patch( '/facultad' , [UniversidadController::class, 'updateFacultad' ]);

//** CARRERAS **/
Route::post( '/carrera' , [UniversidadController::class, 'addCarrera' ]);
Route::patch( '/carrera' , [UniversidadController::class, 'updateCarrera' ]);

//** FACULTADES Y CARRERAS **/
Route::get( '/carreras/{idUniversidad}' , [UniversidadController::class, 'getFacultadesYcarreras' ]);


//** ENTIDADES **/
Route::resource( '/entidad', EntidadController::class );
Route::patch( '/entidad', [EntidadController::class, 'update' ] );


//** TRAMITES **/
Route::get( '/tramite', [ TramiteController::class, 'getListaTramite' ] );
Route::post( '/tramite', [ TramiteController::class, 'addTramite' ] );
Route::patch( '/tramite', [ TramiteController::class, 'updateTramite' ] );


//** HABILITACION DE TRAMITES **/
Route::get( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'getListHabilitacionTramite' ] );
Route::post( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'addHabilitacionTramite' ] );
Route::patch( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'updateHabilitacionTramite' ] );


//** ESTUDIANTE **/
Route::post( '/estudiante', [EstudianteController::class, 'addEstudiante' ] );
Route::get( '/estudiante/{idEstudiante}', [EstudianteController::class, 'getInformacionEstudiante' ] );


//** ANULACIONES **/
Route::get( '/anulacion/{idEstudiante}', [ AnulacionController::class, 'getListaAnulacion' ] );
Route::post( '/anulacion', [ AnulacionController::class, 'addAnulacion' ] );
