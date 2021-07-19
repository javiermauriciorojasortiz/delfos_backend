<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use App\Models\ENUM_AUD;
use App\Models\QUERY_CONF;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class UPGDUI  extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de UPGDs
  function consultarUPGDUIs() {
   $rta = $this->obtenerResultset(QUERY_CONF::_UPU_CONSULTAR);
    $observacion = "Consultar UPGDs -UI";
    $this->insertarAuditoria(Core::$usuarioID,ENUM_AUD::UPDG_UI, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Eliminar la UPGDUI por id
  function eliminarUPGDUI() {
    $lista = $this->obtenerResultset(QUERY_CONF::_UPU_ELIMINAR); 
    $rta = 1;
    $observacion = "UPGDs -UI ID: " . $this->parametros["id"] . ". Nombre: " . $lista[0]->upu_nombre;
    $this->insertarAuditoria(Core::$usuarioID,9, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece la UPGDUI y retorna el número
  function establecerUPGDUI($contactoprincipalid, $contactosecundarioid) {
    $params = $this->parametros;
    $params["contactoprincipalid"] = $contactoprincipalid;
    $params["contactosecundarioid"] = $contactosecundarioid;
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta =  $this->obtenerResultset(QUERY_CONF::_UPU_INSERTAR, $params, true, ["id","contactoprincipal","contactosecundario","departamentoid"]);
      $observacion = "UPGD-UI ID: " . $rta[0]->upu_id . ". Codigo: " . $this->parametros["codigo"] 
                    . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID,9, $observacion, true, "I", ""); //Existe el usuario
    } else {
      $rta =  $this->actualizarData(QUERY_CONF::_UPU_ACTUALIZAR, $params, true,
            ["contactoprincipal","contactosecundario","departamentoid"]);
      $observacion = "UPGD-UI ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
                   . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID,9, $observacion, true, "I", ""); //Existe el usuario
      }
    return $rta;
  }
}
