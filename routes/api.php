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
Route::post('obtenervaloresporcodigocatalogo', [CatalogoController::class, 'obtenerValoresporCodigoCatalogo']);
Route::post('eliminarvalorcatalogo', [CatalogoController::class, 'eliminarValorCatalogo']);
Route::post('establecervalorcatalogo', [CatalogoController::class, 'establecerValorCatalogo']);
//Parametros
Route::post('consultarparametros', [ParametroController::class, 'consultarParametros']);
Route::post('establecerparametro', [ParametroController::class, 'establecerParametro']);
Route::get('obtenerparametroporcodigo', [ParametroController::class, 'obtenerParametroporCodigo']);
//Divipola
Route::post('consultardivipolas', [DivipolaController::class, 'consultarDivipolas']);
Route::post('eliminardivipola', [DivipolaController::class, 'eliminarDivipola']);
Route::post('establecerdivipola', [DivipolaController::class, 'establecerDivipola']);
Route::post('obtenermunicipiosporiddivipola', [DivipolaController::class, 'obtenerMunicipiosporIDDivipola']);
Route::post('establecermunicipio', [DivipolaController::class, 'establecerMunicipio']);
Route::post('eliminarmunicipio', [DivipolaController::class, 'eliminarMunicipio']);
Route::post('obtenerzonasporidmunicipio', [DivipolaController::class, 'obtenerZonasporIDMunicipio']);
Route::post('establecerzona', [DivipolaController::class, 'establecerZona']);
Route::post('eliminarzona', [DivipolaController::class, 'eliminarZona']);
Route::post('obtenerbarriosporidzona', [DivipolaController::class, 'obtenerBarriosporIDZona']);
Route::post('eliminarbarrio', [DivipolaController::class, 'eliminarBarrio']);
Route::post('establecerbarrio', [DivipolaController::class, 'establecerBarrio']);
//EAPB
Route::post('consultareapbs', [EAPBController::class, 'consultarEAPBs']);
//UPGD-UI
Route::post('consultarupgduis', [UPGDUIController::class, 'consultarUPGDUIs']);

