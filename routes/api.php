<?php

use App\Http\Controllers\API\Configuracion\CatalogoController;
use App\Http\Controllers\API\Configuracion\DivipolaController;
use App\Http\Controllers\API\Configuracion\EAPBController;
use App\Http\Controllers\API\Configuracion\GeneralController;
use App\Http\Controllers\API\Configuracion\ParametroController;
use App\Http\Controllers\API\Seguridad\AuditoriaController;
use App\Http\Controllers\API\Seguridad\UsuarioController;
use App\Http\Controllers\API\Configuracion\UPGDUIController;
use App\Http\Controllers\API\Operacion\CasoController;
use App\Http\Controllers\API\Operacion\ComunicacionController;
use App\Http\Controllers\API\Operacion\ContactoController;
use App\Http\Controllers\API\Operacion\ReporteController;
use App\Http\Controllers\API\Operacion\SeguimientoController;
use App\Http\Controllers\API\Operacion\SolicitudAyudaController;
use App\Http\Controllers\API\Operacion\TareaController;
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
Route::get('obtenertiposusuariousuario', [UsuarioController::class, 'obtenerTiposUsuarioUsuario']);
Route::post('enviarcorreo', [UsuarioController::class, 'enviarcorreo']);
Route::get('obtenermenuusuario', [UsuarioController::class, 'obtenerMenuUsuario']);
Route::post('consultarusuarios', [UsuarioController::class, 'consultarUsuarios']);
Route::get('eliminarusuario', [UsuarioController::class, 'eliminarUsuario']);
Route::post('cambiarclave', [UsuarioController::class, 'cambiarClave']);
Route::post('obtenerusuarioporid', [UsuarioController::class, 'obtenerUsuarioporID']);
Route::post('establecerusuario', [UsuarioController::class, 'establecerUsuario']);
Route::get('obtenertiposusuario', [UsuarioController::class, 'obtenerTiposUsuario']);
Route::get('obtenerestadosusuario', [UsuarioController::class, 'obtenerEstadosUsuario']);
Route::get('obtenerrolesusuario', [UsuarioController::class, 'obtenerRolesUsuario']);
Route::post('insertarrolusuario', [UsuarioController::class, 'insertarRolUsuario']);
Route::get('eliminarrolusuario', [UsuarioController::class, 'eliminarRolUsuario']);
Route::post('establecernotificador', [UsuarioController::class, 'establecerNotificador']);
Route::post('establecerresponsable', [UsuarioController::class, 'establecerResponsable']);
Route::get('obtenernotificador', [UsuarioController::class, 'obtenerNotificador']);
Route::get('obtenerresponsable', [UsuarioController::class, 'obtenerResponsable']);
Route::get('obtenerresponsableporidentificacion', [UsuarioController::class, 'obtenerResponsablePorIdentificacion']);
Route::get('autenticarporsesiontemporal', [UsuarioController::class, 'autenticarPorSesionTemporal']);
Route::post('consultarparticipantes', [UsuarioController::class, 'consultarParticipantes']);
//Auditoría
Route::post('consultarauditoria', [AuditoriaController::class, 'consultarAuditoria']);
Route::get('obtenertiposauditoria', [AuditoriaController::class, 'obtenerTiposAuditoria']);
//----------------------------------------------------------------------------------------
//Configuración
//----------------------------------------------------------------------------------------
//Tipo identificación
Route::get('consultartiposidentificacion', [GeneralController::class, 'consultarTiposIdentificacion']);
//Catalogos
Route::post('consultarcatalogos', [CatalogoController::class, 'consultarCatalogos']);
Route::get('obtenervaloresporcodigocatalogo', [CatalogoController::class, 'obtenerValoresporCodigoCatalogo']);
Route::get('obtenervaloresporidcatalogo', [CatalogoController::class, 'obtenerValoresporIdCatalogo']);
Route::get('eliminarvalorcatalogo', [CatalogoController::class, 'eliminarValorCatalogo']);
Route::post('establecervalorcatalogo', [CatalogoController::class, 'establecerValorCatalogo']);
//Parametros
Route::post('consultarparametros', [ParametroController::class, 'consultarParametros']);
Route::post('establecerparametro', [ParametroController::class, 'establecerParametro']);
Route::get('obtenerparametroporcodigo', [ParametroController::class, 'obtenerParametroporCodigo']);
//Divipola
Route::post('consultardivipolas', [DivipolaController::class, 'consultarDivipolas']);
Route::get('eliminardivipola', [DivipolaController::class, 'eliminarDivipola']);
Route::post('establecerdivipola', [DivipolaController::class, 'establecerDivipola']);
Route::get('obtenermunicipiosporiddivipola', [DivipolaController::class, 'obtenerMunicipiosporIDDivipola']);
Route::post('establecermunicipio', [DivipolaController::class, 'establecerMunicipio']);
Route::get('eliminarmunicipio', [DivipolaController::class, 'eliminarMunicipio']);
Route::get('obtenerzonasporidmunicipio', [DivipolaController::class, 'obtenerZonasporIDMunicipio']);
Route::post('establecerzona', [DivipolaController::class, 'establecerZona']);
Route::get('eliminarzona', [DivipolaController::class, 'eliminarZona']);
Route::get('obtenerbarriosporidzona', [DivipolaController::class, 'obtenerBarriosporIDZona']);
Route::get('eliminarbarrio', [DivipolaController::class, 'eliminarBarrio']);
Route::post('establecerbarrio', [DivipolaController::class, 'establecerBarrio']);
Route::get('obtenersecretarias', [DivipolaController::class, 'obtenerSecretarias']);
Route::get('listarpaises', [DivipolaController::class, 'listarPaises']);
Route::get('listardivipolas', [DivipolaController::class, 'listarDivipolas']);
Route::get('listarmunicipios', [DivipolaController::class, 'listarMunicipios']);
Route::get('listarzonas', [DivipolaController::class, 'listarZonas']);
Route::get('listarbarrios', [DivipolaController::class, 'listarBarrios']);
//EAPB
Route::post('consultareapbs', [EAPBController::class, 'consultarEAPBs']);
Route::post('establecereapb', [EAPBController::class, 'establecerEAPB']);
Route::get('eliminareapb', [EAPBController::class, 'eliminarEAPB']);
Route::get('listareapbs', [EAPBController::class, 'listarEAPBs']);
//UPGD-UI
Route::post('consultarupgduis', [UPGDUIController::class, 'consultarUPGDUIs']);
Route::post('establecerupgdui', [UPGDUIController::class, 'establecerUPGDUI']);
Route::get('eliminarupgdui', [UPGDUIController::class, 'eliminarUPGDUI']);
Route::get('listarupgduis', [UPGDUIController::class, 'listarUPGDUIs']);
//Contacto
Route::get('obtenercontactoporid', [ContactoController::class, 'obtenerContactoPorId']);
//Caso
Route::get('consultarcasoporidentificacion', [CasoController::class, 'ConsultarPorIdentificacion']);
Route::get('consultarcasoporresponsable', [CasoController::class, 'ConsultarPorResponsableID']);
Route::get('listarestadospaciente', [CasoController::class, 'listarEstadosPaciente']);
Route::post('establecerpaciente', [CasoController::class, 'establecerPaciente']);
Route::get('obtenercasoporid', [CasoController::class, 'obtenerCasoPorID']);
Route::get('listarhistoricopaciente', [CasoController::class, 'listarHistoricoPaciente']);
Route::get('obtenerresponsablesxcaso', [CasoController::class, 'obtenerResponsablesxCaso']);
//Seguimiento
Route::get('listarseguimientosporcaso', [SeguimientoController::class, 'listarPorCaso']);
Route::post('establecerseguimiento', [SeguimientoController::class, 'establecerSeguimiento']);
Route::get('obtenerdiagnosticoporid',  [SeguimientoController::class, 'obtenerDiagnosticoPorID']);
Route::get('listarproximasevaluaciones',  [SeguimientoController::class, 'listarProximasEvaluaciones']);
//Solicitud ayuda
Route::get('listarsolicitudesporcaso', [SolicitudAyudaController::class, 'listarPorCaso']);
Route::post('crearsolicitudatencion', [SolicitudAyudaController::class, 'crearSolicitudAyuda']);
Route::get('obtenersolicitudporid', [SolicitudAyudaController::class, 'obtenerSolicitudPorID']);
Route::post('guardaratencionsolicitud', [SolicitudAyudaController::class, 'guardarAtencionSolicitud']);
Route::post('confirmartencionsolicitud', [SolicitudAyudaController::class, 'confirmarAtencionSolicitud']);
//Tareas
Route::get('listartareas', [TareaController::class, 'listarTareas']);
//Reportes
Route::post('consultarcasosinteres', [ReporteController::class, 'consultarCasosInteres']);
Route::post('consultarreportegerencial', [ReporteController::class, 'consultarReporteGerencial']);
Route::post('consultarreportegeografico', [ReporteController::class, 'consultarReporteGeografico']);
Route::get('consultarcasosporeapb', [ReporteController::class, 'consultarCasosPorEAPB']);
Route::post('consultarcasosporestado', [ReporteController::class, 'consultarCasosPorEstado']);
Route::post('consultarestadoalertacasos', [ReporteController::class, 'consultarEstadoAlertaCasos']);
//Comunicaciones
Route::post('enviarcorreoscomunicaciones', [ComunicacionController::class, 'enviarCorreos']);
Route::post('enviarsmsscomunicaciones', [ComunicacionController::class, 'enviarSMSs']);