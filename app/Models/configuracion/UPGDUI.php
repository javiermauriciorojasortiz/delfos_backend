<?php

namespace App\Models\Configuracion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_CONF;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class UPGDUI  extends APPBASE{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de UPGDs
  function consultarUPGDUIs() {
   $rta = $this->obtenerResultset(QUERY_CONF::_UPU_CONSULTAR);
    $observacion = "Consultar UPGDs -UI";
    $this->insertarAuditoria(ENUM_AUD::UPDG_UI, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Listar auditorias
  function listarUPGDUIs() {
    return $this->obtenerResultset(QUERY_CONF::_UPU_LISTAR);
  }
  //Lista las UPGDUI por parte del nombre
  function listarUPGDUIsPorNombre() {
    return $this->obtenerResultset(QUERY_CONF::_UPU_LISTARXNOMBRE);
  }
  //Eliminar la UPGDUI por id
  function eliminarUPGDUI() {
    $lista = $this->obtenerResultset(QUERY_CONF::_UPU_ELIMINAR); 
    $rta = 1;
    $observacion = "UPGDs -UI ID: " . $this->parametros["id"] . ". Nombre: " . $lista[0]->upu_nombre;
    $this->insertarAuditoria(ENUM_AUD::UPDG_UI, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece la UPGDUI y retorna el número
  function establecerUPGDUI($contactoprincipalid, $contactosecundarioid) {

    $rta = null;
    if($this->parametros["id"] == 0){
      $params = $this->listarParamRequeridos(["id","contactoprincipal","contactosecundario","departamentoid"]);
      $params["contactoprincipalid"] = $contactoprincipalid;
      $params["contactosecundarioid"] = $contactosecundarioid;
      $rta =  $this->obtenerResultset(QUERY_CONF::_UPU_INSERTAR, $params, true);
      $observacion = "UPGD-UI ID: " . $rta[0]->upu_id . ". Codigo: " . $this->parametros["codigo"] 
                    . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(ENUM_AUD::UPDG_UI, $observacion, true, "I", ""); //Existe el usuario
    } else {
      $params = $this->listarParamRequeridos(["contactoprincipal","contactosecundario","departamentoid"]);
      $params["contactoprincipalid"] = $contactoprincipalid;
      $params["contactosecundarioid"] = $contactosecundarioid;
      $rta =  $this->actualizarData(QUERY_CONF::_UPU_ACTUALIZAR, $params, true);
      $observacion =  "UPGD-UI ID: " . $this->parametros["id"] . 
                      ". Codigo: " . $this->parametros["codigo"] . 
                      ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(ENUM_AUD::UPDG_UI, $observacion, true, "I", ""); //Existe el usuario
    }
    return $rta;
  }
}
