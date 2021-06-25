<?php

namespace App\Http\Controllers\API;

use App\Core\Configuracion\EAPB;
use App\Core\Core;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class EAPBController extends Controller
{
  function consultarEAPBs(Request $request){
    $params = (new Core())->obtenerParametros($request);
    $EAPB = new EAPB();
    return $EAPB->consultarEAPBs($params);
  }
}
