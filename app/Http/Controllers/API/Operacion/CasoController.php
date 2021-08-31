<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Caso;
use App\Models\Seguridad\Responsable;
use App\Models\Seguridad\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase controladora de los catálogos
class CasoController extends Controller
{
  //Consulta lista de pacientes disponible en el sistema asociados a la identifidación
  function ConsultarPorIdentificacion(Request $request){
    $Caso = new Caso($request, ENUM_OPC::CONSULTAR_CASO);
    return $Caso->ConsultarPorIdentificacion();
  }
  //Consultar mi lista de pacientes en la que estoy como responsable
  function ConsultarPorResponsableID(Request $request){
    $Caso = new Caso($request, ENUM_OPC::MIS_PACIENTES);
    return $Caso->ConsultarPorResponsableID();
  }
  //Listar estados paciente
  function listarEstadosPaciente(Request $request) {
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    return $Caso->listarEstadosPaciente(); 
  }
  //Establecer paciente
  function establecerPaciente(Request $request){
    DB::beginTransaction();
    try {
      $Caso = new Caso($request, [ENUM_OPC::OPCION_GENERAL]);
      $Usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
      $Responsable = new Responsable($request, ENUM_OPC::OPCION_GENERAL);
      $idCaso = $Caso->establecerPaciente(); 
      //Si debe modificarse el usuario principal
      if($Caso->parametros["responsableprincipal"]!= null) {
        $paramsUsuario = $Caso->parametros["responsableprincipal"];
        $tiporelacionid = $paramsUsuario["tiporelacionprincipalid"];
        unset($paramsUsuario["tiporelacionprincipalid"]);
        $idUsuario = $Usuario->establecerUsuario($paramsUsuario);
        $Responsable->establecerResponsable($idUsuario, $paramsUsuario["id"]>0?0:1);
        $Caso->establecerRelacionResponsable($idCaso, $idUsuario, $tiporelacionid, true);
      }
      //Si debe modificarse el usuario secundario
      if($Caso->parametros["responsablesecundario"]!= null) {
        $paramsUsuario = $Caso->parametros["responsablesecundario"];
        $tiporelacionid = $paramsUsuario["tiporelacionprincipalid"];
        unset($paramsUsuario["tiporelacionprincipalid"]);
        $idUsuario = $Usuario->establecerUsuario($paramsUsuario);
        $Responsable->establecerResponsable($idUsuario, $paramsUsuario["id"]>0?0:1);
        $Caso->establecerRelacionResponsable($idCaso, $idUsuario, $tiporelacionid, false);
      }
      DB::commit();
      return array("codigo" => 1, "descripcion" => "Exitoso");
    } catch(Exception $e) {
      DB::rollBack();
      return array("codigo" => 0, "descripcion" => $e->__toString());
    }
  }
  //Obtener caso por id
  function obtenerCasoPorID(Request $request){
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    return json_encode($Caso->obtenerPorID()); 
  }
  //Obtener caso por id
  function listarHistoricoPaciente(Request $request){
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    return $Caso->listarHistoricoPaciente(); 
  }
  //Obtener responsables del caso 
  function obtenerResponsablesxCaso(Request $request){
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    return $Caso->obtenerResponsablesxCaso();
  }
  //Activar inactivar caso
  function activarInactivarCaso(Request $request){
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    $rta = null;
    try {
      if($Caso->parametros['causalid']==0){
        $Caso->activarCaso();
      } else {
        $Caso->inactivarCaso();
      }
      $rta = array("codigo" => 1, "descripcion" => "Exitoso");
    } catch (Exception $e) {
      $rta = array("codigo" => 0, "descripcion" => $e->getMessage());
    }
    return $rta;
  }
  //Consultar casos reportados notificador
  function obtenerCasosNotificador(Request $request){
    $Caso = new Caso($request, ENUM_OPC::MIS_TAREAS);
    return $Caso->obtenerCasosNotificador();
  }
}
