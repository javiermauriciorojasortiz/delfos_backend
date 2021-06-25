<?php

namespace App\Http\Controllers\API;

use App\Core\Configuracion\Catalogo;
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

class CatalogoController extends Controller
{
  function consultarCatalogos(Request $request){
    $params = (new Core())->obtenerParametros($request);
    $catalogo = new Catalogo();
    return $catalogo->consultarCatalogos($params);
  }
}