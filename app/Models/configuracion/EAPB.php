<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use App\Models\QUERY_CONF;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class EAPB extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de EAPBs - EPSs del sistema
  function consultarEAPBs() {
    $rta = $this->obtenerResultset(QUERY_CONF::_EAP_CONSULTAR);
    $observacion = "Consultar EAPBS";
    $this->insertarAuditoria(Core::$usuarioID,11, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Eliminar la EPAB por id
  function eliminarEAPB() {
    $rta = $this->obtenerResultset(QUERY_CONF::_EAP_ELIMINAR); 
    $observacion = "EAPB ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->eap_codigo . ". Nombre: " . $rta[0]->eap_nombre;
    $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB($contactoprincipalid, $contactosecundarioid) {
      $params = $this->parametros;
      $params["contactoprincipalid"] = $contactoprincipalid;
      $params["contactosecundarioid"] = $contactosecundarioid;
      if($this->parametros["id"] == 0){
        $rta = $this->obtenerResultset(QUERY_CONF::_EAP_INSERTAR, $params, true, ["id","contactoprincipal","contactosecundario"]);
        $observacion = "EAPB ID: " . $rta[0]->eap_id . ". Codigo: " . $this->parametros["codigo"] 
                      . ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "I", ""); //Existe el usuario
    } else {
        $rta = $this->actualizarData(QUERY_CONF::_EAP_ACTUALIZAR, $params, true,
              ["contactoprincipal","contactosecundario"]);
        $observacion = "EAPB ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
              . ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "M", ""); //Existe el usuario    }
    }
    return $rta;
  }
}
