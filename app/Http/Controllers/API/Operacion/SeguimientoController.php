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
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    return $Seg->listarPorCaso();
  }
  //Obtener Diagnostico por ID
  function obtenerDiagnosticoPorID(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    return $Seg->obtenerDiagnosticoPorID();
  }
  //Insertar seguimiento
  function establecerSeguimiento(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
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
  //Obtener proximos seguimientos
  function listarProximasEvaluaciones(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    return $Seg->listarProximasEvaluaciones();
  }
  //Obtener seguimiento por id
  function obtenerSeguimientoPorID(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    return json_encode($Seg->obtenerSeguimientoPorID());
  }
  //Establecer evaluación seguimiento
  function establecerConfirmacionSeguimiento(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::MIS_TAREAS);
    return $Seg->establecerConfirmacionSeguimiento();
  }
  //Obtener lista de evaluación del último seguimiento por caso
  function listarSeguimientosProximosPorCaso(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::OPCION_GENERAL);
    return $Seg->listarSeguimientosProximosPorCaso();
  }
  //obtenerProximoSeguimientoPorID
  function obtenerProximoSeguimientoPorID(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::MIS_TAREAS);
    return json_encode($Seg->obtenerProximoSeguimientoPorID());
  }
  //confirmar Programacion Proxima Evaluacion
  function confirmarProgramacionProximaEvaluacion(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::MIS_TAREAS);
    try {
      $Seg->confirmarProgramacionProximaEvaluacion();
      return array("codigo" => 1, "descripcion" => "exitoso");
    } catch (Exception $ex) {
      return array("codigo" => 0, "descripcion" => $ex->getMessage());
    }
    
  }
}