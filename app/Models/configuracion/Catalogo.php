<?php

namespace App\Models\Configuracion;

use App\Models\APPBASE;
use App\Models\ENUM_AUD;
use App\Models\QUERY_CONF;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class Catalogo extends APPBASE{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar la lista de catálogos
  function consultarCatalogos() {
    $rta =  $this->obtenerResultset(QUERY_CONF::_CAT_CONSULTAR, $this->listarParamRequeridos());
    $observacion = "Consultar Catalogos";
    $this->insertarAuditoria(ENUM_AUD::CATALOGO, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Obtener Valores catálogo por código
  function obtenerValoresporCodigoCatalogo(){
    return $this->obtenerResultset(QUERY_CONF::_VLC_LISTARXCATCODIGO, $this->listarParamRequeridos());   
  }
  //Obtener Valores catálogo por código
  function obtenerValoresporIdCatalogo(){
    return $this->obtenerResultset(QUERY_CONF::_VLC_LISTARXCATID, $this->listarParamRequeridos());   
  }
  //Elimina el valor de un catálogo
  function eliminarValorCatalogo() {
    $lista = $this->obtenerResultset(QUERY_CONF::_VLC_BORRARXID, $this->listarParamRequeridos()); 
    $rta = 1;
    $observacion =  "CATALOGO ID : " . $this->parametros["id"] . 
                    ". codigo: " . $lista[0]->vlc_codigo . 
                    ". nombre: " . $lista[0]->vlc_nombre;
    $this->insertarAuditoria(ENUM_AUD::CATALOGO, $observacion, true, "E"); //Existe el usuario
    return $rta;
  }
  //Establece el valor del catalogo y retorna el número
  function establecerValorCatalogo(){
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta = $this->obtenerResultset(QUERY_CONF::_VLC_INSERTAR,
                                    $this->listarParamRequeridos(["id"]), 
                                    true);

      $observacion =  "CATALOGO ID:" . $this->parametros["id"] . 
                      ". Valor Catálogo ID: " . $rta[0]->vlc_id . 
                      ". Codigo: " . $this->parametros["codigo"] 
                    . ". Nombre: " . $this->parametros["nombre"];

        $this->insertarAuditoria(ENUM_AUD::CATALOGO, $observacion, true, "I"); //Existe el usuario

    } else {

      $rta = $this->actualizarData(QUERY_CONF::_VLC_ACTUALIZAR, 
                                    $this->listarParamRequeridos(["catalogoid"]), 
                                    true );

      $observacion =  "CATALOGO ID:" . $this->parametros["catalogoid"] . 
                      ". Valor Catálogo ID: " . $this->parametros["id"] . 
                      ". Codigo: " . $this->parametros["codigo"] . 
                      ". Nombre: " . $this->parametros["nombre"];

      $this->insertarAuditoria(ENUM_AUD::CATALOGO, $observacion, true, "I"); //Existe el usuario
    }
    return $rta;
  }
}