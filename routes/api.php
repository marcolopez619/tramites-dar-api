<?php

use App\Models\Traspaso;
use Illuminate\Http\Request;
use App\Models\CambioCarrera;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DarController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AnulacionController;
use App\Http\Controllers\TraspasosController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\ReadmisionController;
use App\Http\Controllers\SuspencionController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\CambioCarreraController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\HabilitacionTramiteController;
use App\Http\Controllers\HabilitacionTramitePorExcepcionController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\PerfilController;

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
Route::get( '/universidad/{idUniversidad}/carreras' , [UniversidadController::class, 'getListaCarrerasByIdUniversidad' ]);
Route::post( '/universidad' , [UniversidadController::class, 'addUniversidad' ]);
Route::patch( '/universidad' , [UniversidadController::class, 'updateUniversidad' ]);
Route::get( '/universidad/{idUniversidad}/all' , [UniversidadController::class, 'getAllInformation' ]);

//** FACULTADES **/
Route::get( '/facultad/{idUniversidad}' , [UniversidadController::class, 'getListaFacultades' ]);
Route::post( '/facultad' , [UniversidadController::class, 'addFacultad' ]);
Route::patch( '/facultad' , [UniversidadController::class, 'updateFacultad' ]);

//** CARRERAS **/
Route::get( '/carrera' , [UniversidadController::class, 'getListaCarreras' ]);
Route::post( '/carrera' , [UniversidadController::class, 'addCarrera' ]);
Route::patch( '/carrera' , [UniversidadController::class, 'updateCarrera' ]);
Route::get( '/carrera/{idFacultad}' , [UniversidadController::class, 'getListaCarrerasByIdFacultad' ]);



//** ENTIDADES **/
Route::resource( '/entidad', EntidadController::class );
Route::patch( '/entidad', [EntidadController::class, 'update' ] );


//** TRAMITES **/
Route::get( '/tramite', [ TramiteController::class, 'getListaTramite' ] );
Route::post( '/tramite', [ TramiteController::class, 'addTramite' ] );
Route::patch( '/tramite', [ TramiteController::class, 'updateTramite' ] );


//** HABILITACION DE TRAMITES EN RANGO DE FECHAS **/
Route::get( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'getListHabilitacionTramite' ] );
Route::post( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'addHabilitacionTramite' ] );
Route::patch( '/habilitacion/tramite', [ HabilitacionTramiteController::class, 'updateHabilitacionTramite' ] );

//** HABILITACION DE TRAMITE DEL ESTUDIANTE POR EXCEPCION **/
Route::post( '/habilitacion/excepcion', [ HabilitacionTramitePorExcepcionController::class, 'addHabilitacionTramitePorExcepcion' ] );
Route::get( '/habilitacion/excepcion', [ HabilitacionTramitePorExcepcionController::class, 'getListaHabilitacionTramitePorExcepcion' ] );


//** ESTUDIANTE **/
Route::post( '/estudiante', [EstudianteController::class, 'addEstudiante' ] );
Route::get( '/estudiante/{idEstudiante}', [EstudianteController::class, 'getInformacionEstudiante' ] );
Route::get( '/estudiante/search/{ru}', [EstudianteController::class, 'getInformacionEstudianteByRu' ] );


//** ANULACIONES **/
Route::get( '/anulacion/{idEstudiante}', [ AnulacionController::class, 'getListaAnulacion' ] );
Route::post( '/anulacion', [ AnulacionController::class, 'addAnulacion' ] );

//** CAMBIO DE CARRERA **/
Route::get( '/cambio/{idEstudiante}', [ CambioCarreraController::class, 'getListaCambioCarrera' ] );
Route::post( '/cambio', [ CambioCarreraController::class, 'addCambioCarrera' ] );

//** SUSPENCIONES **/
Route::get( '/suspencion/{idEstudiante}', [ SuspencionController::class, 'getListaSuspenciones' ] );
Route::post( '/suspencion', [ SuspencionController::class, 'addSuspencion' ] );

//** READMISIONES **/
Route::get( '/readmision/{idEstudiante}', [ ReadmisionController::class, 'getListaReadmisiones' ] );
Route::post( '/readmision', [ ReadmisionController::class, 'addReadmision' ] );

//** TRANSFERENCIAS **/
Route::get( '/transferencia/{idEstudiante}', [ TransferenciaController::class, 'getListaTransferencia' ] );
Route::post( '/transferencia', [ TransferenciaController::class, 'addTransferencia' ] );

//** TRASPASOS **/
Route::get( '/traspaso/{idEstudiante}', [ TraspasosController::class, 'getListaTraspaso' ] );
Route::post( '/traspaso', [ TraspasosController::class, 'addTraspaso' ] );


//** MOTIVOS **/
Route::get( '/motivo', [ MotivoController::class, 'getListaMotivo' ] );




//** MODULO DEL DAR **/

Route::get( '/dar', [ DarController::class, 'getTramitesPorAtender' ] );
Route::get( '/dar/detalle/tramite', [ DarController::class, 'getDetalleTramite' ] );

//** MODULO DEL DIRECTOR **/

Route::get( '/director/{idCarrera}', [ DirectorControll::class, 'getTramitesPorAtender' ] );
// Route::get( '/dar/detalle/tramite', [ DarController::class, 'getDetalleTramite' ] );


//** USUARIOS **/
Route::post( '/usuario', [ UsuarioController::class, 'addUsuario' ] );
Route::patch( '/usuario', [ UsuarioController::class, 'updateUsuario' ] );
Route::delete( '/usuario', [ UsuarioController::class, 'deleteUsuario' ] );
Route::get( '/usuario', [ UsuarioController::class, 'getListaUsuarios' ] );
Route::get( '/usuario/{idUsuario}', [ UsuarioController::class, 'getusuario' ] );


//**  PERFILES **//
Route::get( '/perfiles', [ PerfilController::class, 'getListaPerfiles' ] );


//** LOGIN DEL SISTEMA **/
Route::post( '/login', [ LoginController::class, 'inicializarContexto' ] );

