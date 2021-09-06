<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Parametro;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Comunicacion;
use App\Models\Operacion\Reporte;
use App\Models\Operacion\Tarea;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase controladora de los catálogos
class ComunicacionController extends Controller {
  //Envío de correos
  function enviarCorreos(Request $request){
    $reporte = new Comunicacion($request, ENUM_OPC::COMUNICACIONES);
    return $reporte->enviarCorreos();
  }
  //Envío de mensajes de texto
  function enviarSMSs(Request $request){
    $reporte = new Comunicacion($request, ENUM_OPC::COMUNICACIONES);
    return $reporte->enviarSMSs();
  }
  //Enviar las alertas diarias
  function enviarAlertaDiaria(Request $request){
    $reporte = new Comunicacion($request, ENUM_OPC::OPCION_SIN_SESION);
    $param = new Parametro($request, ENUM_OPC::OPCION_SIN_SESION);
    //Obtener la fecha de ejecución
    $fechacorreo = $param->obtenerParametroporCodigo("FUCRA")[0]->valor;
    if (($timestamp = strtotime($fechacorreo)) === false) {
      $timestamp = strtotime('2020-01-01');
    }
    //Si la fecha actual es mayor que la fecha de la base de datos
    if($timestamp < strtotime(now()->format('Y-m-d'))) {
      $temacorreo = $param->obtenerParametroporCodigo("TMCRA")[0]->valor;
      $textocorreo = $param->obtenerParametroporCodigo("TXCRA")[0]->valor;
      return $reporte->enviarAlertaDiaria($textocorreo,$temacorreo);
    }
    return "0";
  }
}