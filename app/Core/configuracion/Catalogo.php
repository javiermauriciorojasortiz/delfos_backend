<?php

namespace App\Core\Configuracion;

use App\Mail\msgUsuario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


//Clase de gestión del catálogo de valores
class Catalogo {
  function consultarCatalogos($params) {
   return DB::select("SELECT cat_id id, cat_codigo codigo, cat_nombre nombre, cat_descripcion descripcion,
            '' auditoria, cat_id_padre idpadre
            FROM conf.cat_catalogo where LOWER(cat_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
            and LOWER(cat_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'", $params);
  }
}