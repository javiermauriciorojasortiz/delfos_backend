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
    $rta =  $this->obtenerResultset("SELECT cat_id id, cat_codigo codigo, cat_nombre nombre, cat_descripcion descripcion,
             cat_id_padre idpadre, cat_editable editable
            FROM conf.cat_catalogo c
            where LOWER(cat_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
            and LOWER(cat_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
    $observacion = "Consultar Catalogos";
    $this->insertarAuditoria(Core::$usuarioID,12, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Obtener Valores catálogo por código
  function obtenerValoresporCodigoCatalogo(){
    return $this->obtenerResultset("SELECT vlc_id id, vlc_codigo codigo, vlc_nombre nombre, vlc_activo activo,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(v.vlc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      vlc_id_padre idpadre
      FROM conf.cat_catalogo c inner join conf.vlc_valor_catalogo v on v.cat_id = c.cat_id
      left join seg.usr_usuario u on u.usr_id = v.usr_id_auditoria
      where LOWER(cat_codigo) = LOWER(:codigo)");   
  }
  //Obtener Valores catálogo por código
  function obtenerValoresporIdCatalogo(){
    return $this->obtenerResultset("SELECT vlc_id id, vlc_codigo codigo, vlc_nombre nombre, vlc_activo activo,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(v.vlc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      vlc_id_padre idpadre
      FROM conf.cat_catalogo c inner join conf.vlc_valor_catalogo v on v.cat_id = c.cat_id
      left join seg.usr_usuario u on u.usr_id = v.usr_id_auditoria
      where c.cat_id = :id");   
  }
  //Elimina el valor de un catálogo
  function eliminarValorCatalogo() {
    $lista = $this->obtenerResultset("DELETE FROM conf.vlc_valor_catalogo where vlc_id = :id RETURNING vlc_codigo, vlc_nombre"); 
    $rta = 1;
    $observacion = "CATALOGO ID : " . $this->parametros["id"] . ". codigo: " . $lista[0]->vlc_codigo . ". nombre: " . $lista[0]->vlc_nombre;
    $this->insertarAuditoria(Core::$usuarioID, 12, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece el valor del catalogo y retorna el número
  function establecerValorCatalogo(){
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta = $this->obtenerResultset("INSERT INTO conf.vlc_valor_catalogo ( vlc_id, cat_id, vlc_codigo, vlc_nombre, 
            vlc_activo, vlc_fecha_auditoria, usr_id_auditoria) VALUES (nextval('conf.seqvlc'), :catalogoid, :codigo, :nombre, 
            :activo, current_timestamp, :usuario) RETURNING vlc_id", null, true, ["id"]);
        $observacion = "CATALOGO ID:" . $this->parametros["id"] . ". Valor Catálogo ID: " . $rta[0]->vlc_id . ". Codigo: " . $this->parametros["codigo"] 
              . ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(Core::$usuarioID, 12, $observacion, true, "I", ""); //Existe el usuario
      } else {
      $rta = $this->actualizarData("UPDATE conf.vlc_valor_catalogo SET vlc_codigo =:codigo, 
            vlc_nombre = :nombre, vlc_activo = :activo, vlc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
            WHERE vlc_id = :id", null, true, ["catalogoid"]);
      $observacion = "CATALOGO ID:" . $this->parametros["catalogoid"] . ". Valor Catálogo ID: " . $this->parametros["id"] 
            . ". Codigo: " . $this->parametros["codigo"] . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 12, $observacion, true, "I", ""); //Existe el usuario
    }
    return $rta;
  }
}