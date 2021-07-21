<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\EAPB;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Contacto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Controlador de EAPBs
class EAPBController extends Controller
{
  //Expone la consulta de EAPBs
  function consultarEAPBs(Request $request){
    $EAPB = new EAPB($request, ENUM_OPC::EAPB);
    return $EAPB->consultarEAPBs();
  }
  //Listar las UPGDUIs
  function listarEAPBs(Request $request){
    $UPGDUI = new EAPB($request, ENUM_OPC::OPCION_GENERAL);
    return $UPGDUI->listarEAPBs();
  }
  //Eliminar la EPAB por id
  function eliminarEAPB(Request $request) {
    //Inicializar la respuesta
    $rta = [];
    $EAPB = new EAPB($request, ENUM_OPC::EAPB); 
    DB::beginTransaction();    
    try {
      $EAPB->eliminarEAPB(); 
      DB::commit();
      $rta = array("codigo" => 1, "descripcion" => "Exitoso");
    } catch(Exception $ex) {
      DB::rollBack();
      $rta = array("codigo" => 0, "descripcion" => $ex->getMessage());
    }
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB(Request $request) {
    $rta = 0;
    $EAPB = new EAPB($request, ENUM_OPC::EAPB);
    $contactoprincipalid = null; //;
    $contactosecundarioid = null; //$Contacto->establecerContacto();

    DB::beginTransaction();
    try {
      //Si existe contacto principal
      if($EAPB->parametros["contactoprincipal"]!=null){
        $Contacto = new Contacto($request, ENUM_OPC::OPCION_GENERAL);
        $contactoprincipalid = $Contacto->establecerContacto($Contacto->parametros["contactoprincipal"]);
      }
      if($EAPB->parametros["contactosecundario"]!=null){
        $Contacto2 = new Contacto($request, ENUM_OPC::OPCION_GENERAL);
        $contactosecundarioid = $Contacto2->establecerContacto($Contacto2->parametros["contactosecundario"]);
      }
      $rta = $EAPB->establecerEAPB($contactoprincipalid, $contactosecundarioid); 
      DB::commit();
    } catch (Exception $e) {
      throw new Exception("Error estableciendo la entidad." . $e->getMessage());
    }
    return $rta;
  }
}
