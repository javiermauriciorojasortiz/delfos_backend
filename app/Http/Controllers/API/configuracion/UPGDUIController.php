<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\UPGDUI;
use App\Models\Enum\ENUM_AUD;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Contacto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Controlador de UPGD
class UPGDUIController extends Controller
{
  //Expone la lista de UPGDUIs
  function consultarUPGDUIs(Request $request){
    $UPGDUI = new UPGDUI($request, ENUM_OPC::UPGD_UIS);
    return $UPGDUI->consultarUPGDUIs();
  }
  //Listar las UPGDUIs
  function listarUPGDUIs(Request $request){
    $UPGDUI = new UPGDUI($request, ENUM_OPC::OPCION_GENERAL);
    return $UPGDUI->listarUPGDUIs();
  }
  //Eliminar la EPAB por id
  function eliminarUPGDUI(Request $request) {
    //Inicializar la respuesta
    $rta = [];
    $EAPB = new UPGDUI($request, 13);     
    try {
      DB::beginTransaction();
      $EAPB->eliminarUPGDUI(); 
      DB::commit();
      $rta = array("codigo" => 1, "descripcion" => "Exitoso");
    } catch(Exception $ex) {
      DB::rollBack();
      $rta = array("codigo" => 0, "descripcion" => $ex->getMessage());
    }
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerUPGDUI(Request $request) {
    $rta = 0;
    $UPGDUI = new UPGDUI($request, 13);
    $contactoprincipalid = null; //;
    $contactosecundarioid = null; //$Contacto->establecerContacto();

    DB::beginTransaction();
    try {
      //Si existe contacto principal
      if($UPGDUI->parametros["contactoprincipal"]!=null){
        $Contacto = new Contacto($request, 0);
        $contactoprincipalid = $Contacto->establecerContacto($Contacto->parametros["contactoprincipal"]);
      }
      if($UPGDUI->parametros["contactosecundario"]!=null){
        $Contacto2 = new Contacto($request, 0);
        $contactosecundarioid = $Contacto2->establecerContacto($Contacto2->parametros["contactosecundario"]);
      }
      $rta = $UPGDUI->establecerUPGDUI($contactoprincipalid, $contactosecundarioid); 
      DB::commit();
    } catch (Exception $e) {
      throw new Exception("Error estableciendo la entidad." . $e->getMessage());
    }
    return $rta;
  }
}
