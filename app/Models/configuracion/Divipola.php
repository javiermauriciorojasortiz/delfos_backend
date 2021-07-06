<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;


//Clase de gestión del divisiones políticas
class Divipola extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consulta de lista de divipolas
  function consultarDivipolas() {
   return $this->obtenerResultset("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
          pai_nombre nombrepais,
          dvp_ent_territorial entidadTerritorial,
          usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.dvp_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
        from conf.dvp_divipola d
        inner join conf.pai_pais p on p.pai_id = d.pai_id 
        left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
        where LOWER(dvp_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
          and LOWER(dvp_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }
  //Listar divipolas
  function listarDivipolas(){
    return $this->obtenerResultset("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
      pai_id paisid, dvp_ent_territorial entidadTerritorial from conf.dvp_divipola d order by dvp_nombre");
  }
  //Eliminar Divipola por id
  function eliminarDivipola() {
    return $this->actualizarData("DELETE  FROM conf.dvp_divipola where dvp_id = :id"); 
  }
  //Establece el Divipola y retorna el número
  function establecerDivipola() {

    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.dvp_divipola (dvp_id, pai_id, dvp_codigo, dvp_nombre, 
      dvp_ent_territorial, usr_id_auditoria, dvp_fecha_auditoria) 
      VALUES (nextval('conf.seqdvp'), 1, :codigo, :nombre, 
              :entidadterritorial, :usuario, current_timestamp)", null, true, ["id"]);
    } else {
      return $this->actualizarData("UPDATE conf.dvp_divipola SET dvp_codigo =:codigo, 
      dvp_nombre = :nombre, dvp_ent_territorial = :entidadterritorial, dvp_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
      WHERE dvp_id = :id", null, true);
    }
  }
  //Obtener Municipios por id Divipola
  function obtenerMunicipiosporIDDivipola() {
    return $this->obtenerResultset("SELECT mnc_id id, d.dvp_id departamentoid, mnc_nombre nombre,
      mnc_codigo codigo, mnc_ent_territorial entidadTerritorial, dvp_nombre nombredepartamento,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.mnc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.mnc_municipio d
    inner join conf.dvp_divipola v on v.dvp_id = d.dvp_id
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.dvp_id = :id order by mnc_nombre asc");
  }
  //Establece el valor del municipio y retorna el número
  function establecerMunicipio() {
    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.mnc_municipio (mnc_id, dvp_id, mnc_codigo, mnc_nombre, 
      mnc_ent_territorial, usr_id_auditoria, mnc_fecha_auditoria) 
      VALUES (nextval('conf.seqmnc'), :departamentoid, :codigo, :nombre, 
              :entidadterritorial, :usuario, current_timestamp)", null, true, ["id"]);
    } else {
      return $this->actualizarData("UPDATE conf.mnc_municipio SET mnc_codigo =:codigo, 
      mnc_nombre = :nombre, mnc_ent_territorial = :entidadterritorial, mnc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
      WHERE mnc_id = :id", null, true, ["departamentoid"]);
    }
  }
  //Obtener lista de municipios de secretarias
  function obtenerSecretarias() {
    return $this->obtenerResultset("SELECT mnc_id id, dvp_id departamentoid, mnc_nombre nombre, mnc_codigo codigo
    from conf.mnc_municipio where mnc_ent_territorial = cast(1 as bit) order by mnc_nombre asc");
  }
  //Eliminar municipio por id
  function eliminarMunicipio() {
    return $this->actualizarData("DELETE FROM conf.mnc_municipio where mnc_id = :id"); 
  }
  //Obtener Municipios por id Divipola
  function obtenerZonasporIDMunicipio(){
    return $this->obtenerResultset("SELECT zna_id id, mnc_id municipioid, zna_nombre nombre, zna_codigo codigo, 
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.zna_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.zna_zona d
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where mnc_id = :id"); 
  }
//#region Zona
  //Establece el valor del Divipola y retorna el número
  function establecerZona(){

    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.zna_zona (zna_id, mnc_id, zna_codigo, zna_nombre, 
      usr_id_auditoria, zna_fecha_auditoria) 
      VALUES (nextval('conf.seqzna'), :municipioid, :codigo, :nombre, :usuario, current_timestamp)", null, true, ["id"]);
    } else {
      return $this->actualizarData("UPDATE conf.zna_zona SET zna_codigo =:codigo, zna_nombre = :nombre, 
      zna_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE zna_id = :id", null, true);
    }
  }
  //Eliminar zona por id
  function eliminarZona() {
    return $this->actualizarData("DELETE conf.zna_zona where zna_id = :id"); 
  }
  //Obtener Municipios por id Divipola
  function obtenerBarriosporIDZona() {
    return $this->obtenerResultset("SELECT brr_id id, z.zna_id zonaid, brr_nombre nombre,
      brr_codigo codigo, d.vlc_id_upz upzid, vlc_nombre upznombre, zna_nombre nombrezona, brr_nombre_comun nombrecomun,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.brr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.brr_barrio d
    inner join conf.zna_zona z on z.zna_id = d.zna_id
    left join conf.vlc_valor_catalogo v on v.vlc_id = vlc_id_upz
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.zna_id = :id"); 
  }
//#region Barrio
  //Eliminar barrio por id
  function eliminarBarrio() {
    return $this->actualizarData("DELETE FROM conf.brr_barrio where brr_id = :id"); 
  }
  //Establece el valor del Divipola y retorna el número
  function establecerBarrio() {
    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.brr_barrio (brr_id, zna_id, brr_codigo, brr_nombre, 
      usr_id_auditoria, brr_fecha_auditoria, vlc_id_upz, brr_nombre_comun) 
      VALUES (nextval('conf.seqbrr'), :zonaid, :codigo, :nombre, 
      :usuario, current_timestamp, :upzid, :nombrecomun)", null, true, ["id"]);
    } else {
      return $this->actualizarData("UPDATE conf.brr_barrio SET brr_codigo =:codigo, 
      brr_nombre = :nombre, brr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario,
      vlc_id_upz = :upzid, brr_nombre_comun = :nombrecomun, zna_id = :zonaid
      WHERE brr_id = :id", null, true);
    }
  }
//#endregion
}