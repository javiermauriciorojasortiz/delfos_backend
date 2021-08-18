<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Caso;
use App\Models\Operacion\Seguimiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase controladora de los catálogos
class SeguimientoController extends Controller {
  //Consulta lista de seguimientos por caso
  function listarPorCaso(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::CONSULTAR_CASO);
    return $Seg->listarPorCaso();
  }
  //Obtener Diagnostico por ID
  function obtenerDiagnosticoPorID(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::CONSULTAR_CASO);
    return $Seg->obtenerDiagnosticoPorID();
  }
  //Insertar seguimiento
  function establecerSeguimiento(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::CONSULTAR_CASO);
    $Caso = new Caso($request, ENUM_OPC::CONSULTAR_CASO);
    DB::beginTransaction();   
    try {
      $diagnosticoid = $Seg->establecerDiagnostico();
      $seguimientoid = $Seg->establecerSeguimiento($diagnosticoid);
      $Caso->actualizarUltimoSeguimiento($seguimientoid, $Caso->parametros["casoid"]);
      $Seg->establecerProximasEvaluaciones($seguimientoid);
      DB::commit();
      return array("codigo" => 1, "descripcion" => "Exitoso", "data"=> $seguimientoid);
    } catch(Exception $e) {
      DB::rollBack();
      return array("codigo" => 0, "descripcion" => $e->getMessage());
    }
  }
}