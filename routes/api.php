<?php

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


Route::get( '/universidad' , [UniversidadController::class, 'getListUniversidad' ]);
Route::post( '/universidad' , [UniversidadController::class, 'addUniversidad' ]);
Route::patch( '/universidad' , [UniversidadController::class, 'updateUniversidad' ]);


Route::post( '/facultad' , [UniversidadController::class, 'addFacultad' ]);
Route::patch( '/facultad' , [UniversidadController::class, 'updateFacultad' ]);
Route::post( '/carrera' , [UniversidadController::class, 'addCarrera' ]);
Route::patch( '/carrera' , [UniversidadController::class, 'updateCarrera' ]);

Route::get( '/carreras/{idUniversidad}' , [UniversidadController::class, 'getFacultadesYcarreras' ]);
