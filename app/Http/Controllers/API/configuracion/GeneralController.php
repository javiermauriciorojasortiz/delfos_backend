<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\General;
use App\Models\Enum\ENUM_OPC;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class GeneralController extends Controller
{
  //Consulta lista de catálogos disponible en el sistema
  function consultarTiposIdentificacion(Request $request){
    $general = new General($request, ENUM_OPC::OPCION_SIN_SESION);
    return $general->consultarTiposIdentificacion();
  }
}