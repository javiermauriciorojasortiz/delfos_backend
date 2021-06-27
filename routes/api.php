<?php

use App\Http\Controllers\API\Configuracion\CatalogoController;
use App\Http\Controllers\API\Configuracion\DivipolaController;
use App\Http\Controllers\API\Configuracion\EAPBController;
use App\Http\Controllers\API\Configuracion\ParametroController;
use App\Http\Controllers\API\Seguridad\AuditoriaController;
use App\Http\Controllers\API\Seguridad\UsuarioController;
use App\Http\Controllers\API\Configuracion\UPGDUIController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('consultarusuarios/{id}', [UsuarioController::class, 'buscar']);
//----------------------------------------------------------------------------------------
//Seguridad
//----------------------------------------------------------------------------------------
//Usuario
Route::post('login', [UsuarioController::class, 'autenticar']);
Route::get('consultarusuarios', [UsuarioController::class, 'buscar']);
Route::post('autenticar', [UsuarioController::class, 'autenticarporToken']);
Route::post('enviarcorreo', [UsuarioController::class, 'enviarcorreo']);
Route::get('obtenermenuusuario', [UsuarioController::class, 'obtenerMenuUsuario']);
Route::post('consultarusuarios', [UsuarioController::class, 'consultarUsuarios']);
Route::get('eliminarusuario', [UsuarioController::class, 'eliminarUsuario']);
Route::post('cambiarclave', [UsuarioController::class, 'cambiarClave']);
Route::post('obtenerusuarioporid', [UsuarioController::class, 'obtenerUsuarioporID']);
Route::post('establecerusuario', [UsuarioController::class, 'establecerUsuario']);
Route::get('obtenertiposusuario', [UsuarioController::class, 'obtenerTiposUsuario']);
Route::get('obtenerestadosusuario', [UsuarioController::class, 'obtenerEstadosUsuario']);
//Auditoría
Route::post('consultarauditoria', [AuditoriaController::class, 'consultarAuditoria']);
Route::get('obtenertiposauditoria', [AuditoriaController::class, 'obtenerTiposAuditoria']);
//----------------------------------------------------------------------------------------
//Configuración
//----------------------------------------------------------------------------------------
//Catalogos
Route::post('consultarcatalogos', [CatalogoController::class, 'consultarCatalogos']);
//Parametros
Route::post('consultarparametros', [ParametroController::class, 'consultarParametros']);
Route::post('establecerparametro', [ParametroController::class, 'establecerParametro']);
Route::get('obtenerparametroporcodigo', [ParametroController::class, 'obtenerParametroporCodigo']);
//Divipola
Route::post('consultardivipolas', [DivipolaController::class, 'consultarDivipolas']);
//EAPB
Route::post('consultareapbs', [EAPBController::class, 'consultarEAPBs']);
//UPGD-UI
Route::post('consultarupgduis', [UPGDUIController::class, 'consultarUPGDUIs']);

