<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class Catalogo extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar la lista de catálogos
  function consultarCatalogos() {
   return $this->obtenerResultset("SELECT cat_id id, cat_codigo codigo, cat_nombre nombre, cat_descripcion descripcion,
            '' auditoria, cat_id_padre idpadre
            FROM conf.cat_catalogo where LOWER(cat_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
            and LOWER(cat_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'", $this->parametros);
  }
}