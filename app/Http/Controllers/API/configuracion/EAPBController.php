<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\EAPB;
use App\Models\Operacion\Contacto;
use Exception;
use Illuminate\Http\Request;

//Controlador de EAPBs
class EAPBController extends Controller
{
  //Expone la consulta de EAPBs
  function consultarEAPBs(Request $request){
    $EAPB = new EAPB($request, 13);
    return $EAPB->consultarEAPBs();
  }
  //Eliminar la EPAB por id
  function eliminarEAPB(Request $request) {
    //Inicializar la respuesta
    $rta = [];
    $EAPB = new EAPB($request, 13);     
    try {
      $EAPB->iniciarTransaccion();
      $EAPB->eliminarEAPB(); 
      $EAPB->serializarTransaccion();
      $rta = array("codigo" => 1, "descripcion" => "Exitoso");
    } catch(Exception $ex) {
      $EAPB->abortarTransaccion();
      $rta = array("codigo" => 0, "descripcion" => $ex->getMessage());
    }
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB(Request $request) {
    $rta = 0;
    $EAPB = new EAPB($request, 13);
    $contactoprincipalid = null; //;
    $contactosecundarioid = null; //$Contacto->establecerContacto();

    $EAPB->iniciarTransaccion();
    //Si existe contacto principal
    if($EAPB->parametros["contactoprincipal"]!=null){
      $Contacto = new Contacto($request, 0);
      $contactoprincipalid = $Contacto->establecerContacto($Contacto->parametros["contactoprincipal"]);
    }
    if($EAPB->parametros["contactosecundario"]!=null){
      $Contacto2 = new Contacto($request, 0);
      $contactosecundarioid = $Contacto2->establecerContacto($Contacto2->parametros["contactosecundario"]);
    }
    $rta = $EAPB->establecerEAPB($contactoprincipalid, $contactosecundarioid); 
    $EAPB->serializarTransaccion();

    return $rta;
  }
}
