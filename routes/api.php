<?php

use App\Http\Controllers\API\UsuarioController;
use App\Models\Seguridad\UsuarioModel;
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

Route::get('usuarios', [UsuarioController::class, 'index']);
Route::get('login/{id}/{pass}', [UsuarioController::class, 'show']);

// Route::get('consultarusuarios/{id}', [UsuarioController::class, 'buscar']);
Route::get('consultarusuarios', [UsuarioController::class, 'buscar']);
Route::post('login', [UsuarioController::class, 'autenticar']);

Route::post('consultarauditoria', [UsuarioController::class, 'consultarAuditoria']);