<?php

namespace App\Models\Configuracion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_CONF;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class EAPB extends APPBASE{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de EAPBs - EPSs del sistema
  function consultarEAPBs() {
    $rta = $this->obtenerResultset(QUERY_CONF::_EAP_CONSULTAR, $this->listarParamRequeridos());
    $observacion = "Consultar EAPBS";
    $this->insertarAuditoria(ENUM_AUD::EAPB, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Listar EAPBs disponibles
  function listarEAPBs() {
    return $this->obtenerResultset(QUERY_CONF::_EAP_LISTAR);
  }
  //Eliminar la EPAB por id
  function eliminarEAPB() {
    $rta = $this->obtenerResultset(QUERY_CONF::_EAP_ELIMINAR, $this->listarParamRequeridos()); 
    $observacion = "EAPB ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->eap_codigo . ". Nombre: " . $rta[0]->eap_nombre;
    $this->insertarAuditoria(ENUM_AUD::EAPB, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB($contactoprincipalid, $contactosecundarioid) {
    //Definir si se inserta o actualiza
    if($this->parametros["id"] == 0){
        $params = $this->listarParamRequeridos(["id","contactoprincipal","contactosecundario"]);
        $params["contactoprincipalid"] = $contactoprincipalid;
        $params["contactosecundarioid"] = $contactosecundarioid;
        $rta = $this->obtenerResultset(QUERY_CONF::_EAP_INSERTAR, $params, true);
        $observacion = "EAPB ID: " . $rta[0]->eap_id . 
                      ". Codigo: " . $this->parametros["codigo"] . 
                      ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(ENUM_AUD::EAPB, $observacion, true, "I", ""); //No Existe
    } else {
      $params = $this->listarParamRequeridos(["contactoprincipal","contactosecundario"]);
      $params["contactoprincipalid"] = $contactoprincipalid;
      $params["contactosecundarioid"] = $contactosecundarioid;
      $rta = $this->actualizarData(QUERY_CONF::_EAP_ACTUALIZAR, $params, true);
        $observacion =  "EAPB ID: " . $this->parametros["id"] . 
                        ". Codigo: " . $this->parametros["codigo"] . 
                        ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(ENUM_AUD::EAPB, $observacion, true, "M", ""); //Existe
    }
    return $rta;
  }
}
