<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Reporte;
use App\Models\Operacion\Tarea;
use Exception;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class ReporteController extends Controller {
  //Consulta lista de tareas
  function consultarCasosInteres(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::CASOS_INTERES);
    return $reporte->consultarCasosInteres();
  }
  //Consultar reporte gerencial
  function consultarReporteGerencial(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::REPORTE_GERENCIAL);
    return $reporte->consultarReporteGerencial();
  }
  //Consultar reporte Geográfico
  function consultarReporteGeografico(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::CONSULTA_GEOGRAFICA);
    return $reporte->consultarReporteGeografico();
  }
  //Tablero de control consultar Casos Por EAPB
  function consultarCasosPorEAPB(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::TABLERO_CONTROL);
    return $reporte->consultarCasosPorEAPB();
  }
  //Tablero de control Consultar  casos por Estado
  function consultarCasosPorEstado(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::TABLERO_CONTROL);
    return $reporte->consultarCasosPorEstado();
  }
  //Tablero de control Consultar casos por estado alerta
  function consultarEstadoAlertaCasos(Request $request){
    $reporte = new Reporte($request, ENUM_OPC::TABLERO_CONTROL);
    return $reporte->consultarEstadoAlertaCasos();
  }
}