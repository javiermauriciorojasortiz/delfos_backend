<?php

namespace App\Models\Seguridad;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_SEG;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Responsable extends APPBASE {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Inserta o actualiza al responsable
  public function establecerResponsable(int $id, bool $nuevo){
    $autorizaemail = true;
    $autorizasms = true;
    if(array_key_exists('autoriza_email', $this->parametros)) $autorizaemail =$this->parametros["autoriza_sms"];
    if(array_key_exists("autoriza_sms", $this->parametros)) $autorizaemail = $this->parametros["autoriza_sms"];
    $params = array("id" => $id, "autoriza_email" => $autorizaemail, "autoriza_sms" => $autorizasms);
    $rta = null;
    if($nuevo) {//Insertar
      if($this->usuarioID <= 0) $this->usuarioID = $id;
      $rta =  $this->actualizarData(QUERY_SEG::_RPS_INSERTAR, $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria(ENUM_AUD::RESPONSABLE, $observacion, true, "I", ""); //Existe el usuario  
    } else { //Actualizar
      
      $rta =  $this->actualizarData(QUERY_SEG::_RPS_ACTUALIZAR, $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria(ENUM_AUD::RESPONSABLE, $observacion, true, "M", ""); //Existe el usuario  
    }
    return $rta;
  }
  //Obtener usuario por id
  public function obtenerResponsable(){
    return $this->obtenerResultset(QUERY_SEG::_RPS_OBTENERXID)[0];
  }
  //Obtener usuario por identificador
  public function obtenerResponsablePorIdentificacion(){
    return $this->obtenerResultset(QUERY_SEG::_RPS_OBTENERXIDENTIFICACION);
  }
  
}