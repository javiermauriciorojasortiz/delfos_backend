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
            and LOWER(cat_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }
  //Obtener Valores catálogo por código
  function obtenerValoresporCodigoCatalogo(){
    return $this->obtenerResultset("SELECT vlc_id id, vlc_codigo codigo, vlc_nombre nombre, vlc_activo activo
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(v.vlc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      vlc_id_padre idpadre
      FROM conf.cat_catalogo c inner join conf.vlc_valor_catalogo v on v.cat_id = c.cat_id
      left join seg.usr_usuario u on u.usr_id = v.usr_id_auditoria
      where LOWER(cat_codigo) = LOWER(:codigo,)")[0];   
  }
  //Elimina el valor de un catálogo
  function eliminarValorCatalogo() {
    return $this->actualizarData("DELETE conf.vlc_valor_catalogo where vlc_id = :id"); 
  }
  //Establece el valor del catalogo y retorna el número
  function establecerValorCatalogo(){

    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.vlc_valor_catalogo ( vlc_id, cat_id, vlc_codigo, vlc_nombre, 
        vlc_activo, vlc_fecha_auditoria, usr_id_auditoria) VALUES (nextval('conf.secvlc'), :catalogoid, :codigo, :nombre, 
              :activo, current_timestamp, :usuario)", null, true);
    } else {
      return $this->actualizarData("UPDATE conf.vlc_valor_catalogo SET vlc_codigo =:codigo, 
      vlc_nombre = :nombre, vlc_activo = :activo, vlc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
      WHERE vlc_id = :id", null, true);
    }
  }
}