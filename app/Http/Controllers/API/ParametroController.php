<?php

namespace App\Http\Controllers\API;

use App\Core\Configuracion\Catalogo;
use App\Core\Configuracion\Parametro;
use App\Core\Core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seguridad\UsuarioModel;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\DB;
use App\Core\Seguridad\Usuario;
use App\Core\Seguridad\respuestaAPI;
use App\Mail\Usuario as MailUsuario;
use DateTime;
use Exception;

class ParametroController extends Controller
{
  //Consulta general de parámetros generales
  function consultarParametros(Request $request){
    $params = (new Core())->obtenerParametros($request);
    $catalogo = new Parametro();
    return $catalogo->consultarParametros($params);
  }
}