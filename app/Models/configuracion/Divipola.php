<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use App\Models\QUERY_CONF;
use App\Models\QUERY_OPER;
use Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;


//Clase de gestión del divisiones políticas
class Divipola extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consulta de lista de divipolas
  function consultarDivipolas() {
    $rta = $this->obtenerResultset(QUERY_CONF::_DVP_CONSULTAR);
    $observacion = "Consultar Departamento";
    $this->insertarAuditoria(Core::$usuarioID,13, $observacion, true, "C", ""); 
    return $rta;
  }
  //Listar divipolas
  function listarDivipolas(){
    return $this->obtenerResultset(QUERY_CONF::_DVP_LISTAR);
  }
  //Eliminar Divipola por id
  function eliminarDivipola() {
    try {
      $rta = $this->obtenerResultset(QUERY_CONF::_DVP_ELIMINAR); 
      $observacion = "Departamento ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->dvp_codigo . ". Nombre: " . $rta[0]->dvp_nombre;;;
      $this->insertarAuditoria(Core::$usuarioID, 13, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
  }
  //Establece el Divipola y retorna el número
  function establecerDivipola() {
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta = $this->obtenerResultset(QUERY_CONF::_DVP_INSERTAR, null, true, ["id"]);
      $observacion = "Departamento ID: " . $rta[0]->dvp_id . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 13, $observacion, true, "I", ""); 
    } else {
      $rta = $this->actualizarData(QUERY_CONF::_DVP_ACTUALIZAR, null, true);
      $observacion = "Departamento ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 13, $observacion, true, "M", "");  
    }
    return $rta;
  }
  //Obtener Municipios por id Divipola
  function obtenerMunicipiosporIDDivipola() {
    $rta = $this->obtenerResultset(QUERY_CONF::_MNC_OBTENERPORIDDVP);
    return $rta;
  }
  //Establece el valor del municipio y retorna el número
  function establecerMunicipio() {
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta = $this->obtenerResultset(QUERY_CONF::_MNC_INSERTAR, null, true, ["id"]);
      $observacion = "Municipio ID: " . $rta[0]->mnc_id . ". Codigo: " . $this->parametros["codigo"] 
              . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "I", ""); 
    } else {
      $rta = $this->actualizarData(QUERY_CONF::_MNC_ACTUALIZAR, null, true, ["departamentoid"]);
      $observacion = "Municipio ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "M", "");     
    }
    return $rta;
  }
  //Obtener lista de municipios de secretarias
  function obtenerSecretarias() {
    return $this->obtenerResultset(QUERY_CONF::_MNC_OBTENERSECRETARIAS);
  }
  //Eliminar municipio por id
  function eliminarMunicipio() {
    try {
      $rta = $this->obtenerResultset(QUERY_CONF::_MNC_ELIMINAR); 
      $observacion = "Municipio ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->mnc_codigo . ". Nombre: " . $rta[0]->mnc_nombre;
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
  }
  //Obtener Municipios por id Divipola
  function obtenerZonasporIDMunicipio(){
    return $this->obtenerResultset(QUERY_CONF::_ZNA_OBTENERXMNC); 
  }
//#region Zona
  //Establece el valor del Divipola y retorna el número
  function establecerZona(){
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta =  $this->obtenerResultset(QUERY_CONF::_ZNA_ACTUALIZAR, null, true, ["id"]);
      $observacion = "Zona ID: " . $rta[0]->zna_id . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "I", ""); 
    } else {
      $rta =  $this->actualizarData(QUERY_CONF::_ZNA_ACTUALIZAR, null, true, ["municipioid"]);
      $observacion = "Zona ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "M", "");     
    }
    return $rta;
  }
  //Eliminar zona por id
  function eliminarZona() {
    try {
      $rta = $this->obtenerResultset(QUERY_CONF::_ZNA_ELIMINAR); 
      $observacion = "Zona ID : " . $this->parametros["id"]  . ". Código: " . $rta[0]->zna_codigo . ". Nombre: " . $rta[0]->zna_nombre;
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
  }
  //Obtener Municipios por id Divipola
  function obtenerBarriosporIDZona() {
    $rta = $this->obtenerResultset(QUERY_CONF::_BRR_OBTENERXZNA); 
    return $rta;
  }
//#region Barrio
  //Eliminar barrio por id
  function eliminarBarrio() {
    try {
      $rta = $this->obtenerResultset(QUERY_CONF::_BRR_ELIMINAR); 
      $observacion = "Barrio ID : " . $this->parametros["id"]  . ". Código: " . $rta[0]->brr_codigo . ". Nombre: " . $rta[0]->brr_nombre;;
      $this->insertarAuditoria(Core::$usuarioID, 4, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
  }
  //Establece el valor del Divipola y retorna el número
  function establecerBarrio() {
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta =  $this->obtenerResultset(QUERY_CONF::_BRR_INSERTAR, null, true, ["id"]);

      $observacion = "Barrio ID: " . $rta[0]->brr_id . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 4, $observacion, true, "I", ""); 
    } else {
      $rta =  $this->actualizarData(QUERY_CONF::_BRR_ACTUALIZAR, null, true);

      $observacion = "Barrio ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
      . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 4, $observacion, true, "M", ""); //Existe el elemento
    }
    return $rta;
  }
//#endregion
}