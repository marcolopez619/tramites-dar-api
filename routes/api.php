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


Route::post( '/universidad' , [UniversidadController::class, 'addUniversidad' ]);
Route::get( '/universidad' , [UniversidadController::class, 'getListUniversidad' ]);
Route::get( '/carreras/{idUniversidad}' , [UniversidadController::class, 'getFacultadesYcarreras' ]);

Route::post( '/facultad' , [UniversidadController::class, 'addFacultad' ]);
Route::post( '/carrera' , [UniversidadController::class, 'addCarrera' ]);
