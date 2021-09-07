<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Parametro;
use App\Models\Enum\ENUM_OPC;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
  //Consulta general de parámetros generales
  function consultarParametros(Request $request){
    $parametro = new Parametro($request, 14);
    return $parametro->consultarParametros();
  }
  //Establecer nuevo valor parámetro 
  function establecerParametro(Request $request){
    $parametro = new Parametro($request, 14);
    return $parametro->establecerParametro();
  }
  //Obtener un parámetro por su código
  function obtenerParametroporCodigo(Request $request){
    $parametro = new Parametro($request, ENUM_OPC::OPCION_GENERAL);
    return $parametro->obtenerParametroporCodigo();
  }
}