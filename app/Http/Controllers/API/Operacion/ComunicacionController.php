<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Comunicacion;
use App\Models\Operacion\Reporte;
use App\Models\Operacion\Tarea;
use Exception;
use Illuminate\Http\Request;

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
}