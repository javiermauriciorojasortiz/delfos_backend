<?php

namespace App\Models\Configuracion;

use App\Models\APPBASE;
use App\Models\Query\QUERY_CONF;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class General extends APPBASE{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar la lista de catálogos
  function consultarTiposIdentificacion() {
    $rta =  $this->obtenerResultset(QUERY_CONF::_TID_CONSULTAR, $this->listarParamRequeridos());
    return $rta;
  }
}