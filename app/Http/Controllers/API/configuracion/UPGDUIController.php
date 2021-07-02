<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\UPGDUI;
use App\Models\Operacion\Contacto;
use Exception;
use Illuminate\Http\Request;

//Controlador de UPGD
class UPGDUIController extends Controller
{
  //Expone la lista de UPGDUIs
  function consultarUPGDUIs(Request $request){
    $UPGDUI = new UPGDUI($request, 15);
    return $UPGDUI->consultarUPGDUIs();
  }
  //Eliminar la EPAB por id
  function eliminarUPGDUI(Request $request) {
    //Inicializar la respuesta
    $rta = [];
    $EAPB = new UPGDUI($request, 13);     
    try {
      $EAPB->iniciarTransaccion();
      $EAPB->eliminarUPGDUI(); 
      $EAPB->serializarTransaccion();
      $rta = array("codigo" => 1, "descripcion" => "Exitoso");
    } catch(Exception $ex) {
      $EAPB->abortarTransaccion();
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
  
      $UPGDUI->iniciarTransaccion();
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
      $UPGDUI->serializarTransaccion();
  
      return $rta;
    }
}
