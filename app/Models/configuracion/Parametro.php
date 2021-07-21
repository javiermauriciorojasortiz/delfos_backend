<?php

namespace App\Models\Configuracion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_CONF;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Parametro extends APPBASE{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  
  //Consultar la lista de parámetros generales de la base de datos
  function consultarParametros() {
    $rta = $this->obtenerResultset(QUERY_CONF::_PRG_CONSULTAR, $this->listarParamRequeridos());
      $observacion = "Consultar parámetros generales";
      $this->insertarAuditoria(ENUM_AUD::PARAMETRO, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Establece un cambio en el valor del parámetro
  function establecerParametro() {
    $rta = $this->actualizarData(QUERY_CONF::_PRG_ACTUALIZAR, $this->listarParamRequeridos(), true);
    $observacion = "Parámetro : " . $this->parametros["id"] . ". Valor: " . $this->parametros["valor"];
    $this->insertarAuditoria(ENUM_AUD::PARAMETRO, $observacion, true, "M", ""); //Existe el usuario
    return $rta;
  }
  //Obtener un parámetro por su código()
  function obtenerParametroporCodigo($codigo = null){
    $params = $this->parametros;
    if($codigo!= null) {
      unset($params);
      $params["codigo"] = $codigo;
    } 
    return $this->obtenerResultset(QUERY_CONF::_PRG_OBTENERXCODIGO, $params);  
  }
}