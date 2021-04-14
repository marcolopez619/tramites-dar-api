<?php

use App\Http\Controllers\EntidadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversidadController;


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
