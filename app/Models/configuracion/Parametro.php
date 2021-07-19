<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use App\Models\QUERY_CONF;
use App\Models\QUERY_OPER;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Parametro extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  
  //Consultar la lista de parámetros generales de la base de datos
  function consultarParametros() {
    $rta = $this->obtenerResultset(QUERY_CONF::_PRG_CONSULTAR);
      $observacion = "Consultar parámetros generales";
      $this->insertarAuditoria(Core::$usuarioID,10, $observacion, true, "C", ""); //Existe el usuario

    return $rta;
  }
  //Establece un cambio en el valor del parámetro
  function establecerParametro() {
    $rta = $this->actualizarData(QUERY_CONF::_PRG_ACTUALIZAR,null, true);
    $observacion = "Parámetro : " . $this->parametros["id"] . ". Valor: " . $this->parametros["valor"];
    $this->insertarAuditoria(Core::$usuarioID,10, $observacion, true, "M", ""); //Existe el usuario
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