<?php

namespace App\Models\Configuracion;

use App\Models\Core;
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
    $rta = $this->obtenerResultset("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
          pai_nombre nombrepais,
          dvp_ent_territorial entidadTerritorial,
          usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.dvp_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
        from conf.dvp_divipola d
        inner join conf.pai_pais p on p.pai_id = d.pai_id 
        left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
        where LOWER(dvp_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
          and LOWER(dvp_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
    $observacion = "Consultar Departamento";
    $this->insertarAuditoria(Core::$usuarioID,13, $observacion, true, "C", ""); 
    return $rta;
  }
  //Listar divipolas
  function listarDivipolas(){
    return $this->obtenerResultset("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
      pai_id paisid, dvp_ent_territorial entidadTerritorial from conf.dvp_divipola d order by dvp_nombre");
  }
  //Eliminar Divipola por id
  function eliminarDivipola() {
    try {
      $rta = $this->obtenerResultset("DELETE  FROM conf.dvp_divipola where dvp_id = :id RETURNING dvp_id, dvp_codigo, dvp_nombre"); 
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
      $rta = $this->obtenerResultset("INSERT INTO conf.dvp_divipola (dvp_id, pai_id, dvp_codigo, dvp_nombre, 
      dvp_ent_territorial, usr_id_auditoria, dvp_fecha_auditoria) 
      VALUES (nextval('conf.seqdvp'), 1, :codigo, :nombre, 
              :entidadterritorial, :usuario, current_timestamp) RETURNING dvp_id", null, true, ["id"]);
      $observacion = "Departamento ID: " . $rta[0]->dvp_id . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 13, $observacion, true, "I", ""); 
    } else {
      $rta = $this->actualizarData("UPDATE conf.dvp_divipola SET dvp_codigo =:codigo, 
      dvp_nombre = :nombre, dvp_ent_territorial = :entidadterritorial, dvp_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
      WHERE dvp_id = :id", null, true);
      $observacion = "Departamento ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 13, $observacion, true, "M", "");  
    }
    return $rta;
  }
  //Obtener Municipios por id Divipola
  function obtenerMunicipiosporIDDivipola() {
    $rta = $this->obtenerResultset("SELECT mnc_id id, d.dvp_id departamentoid, mnc_nombre nombre,
      mnc_codigo codigo, (mnc_ent_territorial = B'1') entidadTerritorial, dvp_nombre nombredepartamento,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.mnc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.mnc_municipio d
    inner join conf.dvp_divipola v on v.dvp_id = d.dvp_id
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.dvp_id = :id order by mnc_nombre asc");
    return $rta;
  }
  //Establece el valor del municipio y retorna el número
  function establecerMunicipio() {
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta = $this->obtenerResultset("INSERT INTO conf.mnc_municipio (mnc_id, dvp_id, mnc_codigo, mnc_nombre, 
      mnc_ent_territorial, usr_id_auditoria, mnc_fecha_auditoria) 
      VALUES (nextval('conf.seqmnc'), :departamentoid, :codigo, :nombre, 
              coalesce(:entidadterritorial, cast(0 as bit)), :usuario, current_timestamp) RETURNING mnc_id", null, true, ["id"]);
      $observacion = "Municipio ID: " . $rta[0]->mnc_id . ". Codigo: " . $this->parametros["codigo"] 
              . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "I", ""); 
    } else {
      $rta = $this->actualizarData("UPDATE conf.mnc_municipio SET mnc_codigo =:codigo, 
      mnc_nombre = :nombre, mnc_ent_territorial = coalesce(:entidadterritorial,cast(0 as bit)), mnc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
      WHERE mnc_id = :id", null, true, ["departamentoid"]);
      $observacion = "Municipio ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "M", "");     
    }
    return $rta;
  }
  //Obtener lista de municipios de secretarias
  function obtenerSecretarias() {
    return $this->obtenerResultset("SELECT mnc_id id, dvp_id departamentoid, mnc_nombre nombre, mnc_codigo codigo
    from conf.mnc_municipio where mnc_ent_territorial = cast(1 as bit) order by mnc_nombre asc");
  }
  //Eliminar municipio por id
  function eliminarMunicipio() {
    try {
      $rta = $this->obtenerResultset("DELETE FROM conf.mnc_municipio where mnc_id = :id RETURNING mnc_codigo, mnc_nombre"); 
      $observacion = "Municipio ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->mnc_codigo . ". Nombre: " . $rta[0]->mnc_nombre;
      $this->insertarAuditoria(Core::$usuarioID, 14, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
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
    $rta = null;
    if($this->parametros["id"] == 0){
      $rta =  $this->obtenerResultset("INSERT INTO conf.zna_zona (zna_id, mnc_id, zna_codigo, zna_nombre, 
      usr_id_auditoria, zna_fecha_auditoria) 
      VALUES (nextval('conf.seqzna'), :municipioid, :codigo, :nombre, :usuario, current_timestamp) RETURNING zna_id", 
      null, true, ["id"]);
      $observacion = "Zona ID: " . $rta[0]->zna_id . ". Codigo: " . $this->parametros["codigo"] 
          . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "I", ""); 
    } else {
      $rta =  $this->actualizarData("UPDATE conf.zna_zona SET zna_codigo =:codigo, zna_nombre = :nombre, 
      zna_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE zna_id = :id", null, true, ["municipioid"]);
      $observacion = "Zona ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "M", "");     
    }
    return $rta;
  }
  //Eliminar zona por id
  function eliminarZona() {
    try {
      $rta = $this->obtenerResultset("DELETE from conf.zna_zona where zna_id = :id RETURNING zna_id, zna_codigo, zna_nombre"); 
      $observacion = "Zona ID : " . $this->parametros["id"]  . ". Código: " . $rta[0]->zna_codigo . ". Nombre: " . $rta[0]->zna_nombre;
      $this->insertarAuditoria(Core::$usuarioID, 15, $observacion, true, "E", ""); 
      return array("codigo" => 1, "descripcion" =>"Eliminación exitosa");
    } catch (Exception $e) {
      return array("codigo" => 2, "descripcion" => $e->getMessage());
    }
  }
  //Obtener Municipios por id Divipola
  function obtenerBarriosporIDZona() {
    $rta = $this->obtenerResultset("SELECT brr_id id, z.zna_id zonaid, brr_nombre nombre,
      brr_codigo codigo, d.vlc_id_upz upzid, vlc_nombre upznombre, zna_nombre nombrezona, brr_nombre_comun nombrecomun,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.brr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.brr_barrio d
    inner join conf.zna_zona z on z.zna_id = d.zna_id
    left join conf.vlc_valor_catalogo v on v.vlc_id = vlc_id_upz
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.zna_id = :id"); 
    return $rta;
  }
//#region Barrio
  //Eliminar barrio por id
  function eliminarBarrio() {
    try {
      $rta = $this->obtenerResultset("DELETE FROM conf.brr_barrio where brr_id = :id RETURNING brr_id, brr_codigo, brr_nombre"); 
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
      $rta =  $this->obtenerResultset("INSERT INTO conf.brr_barrio (brr_id, zna_id, brr_codigo, brr_nombre, 
      usr_id_auditoria, brr_fecha_auditoria, vlc_id_upz, brr_nombre_comun) 
      VALUES (nextval('conf.seqbrr'), :zonaid, :codigo, :nombre, 
      :usuario, current_timestamp, :upzid, :nombrecomun) RETURNING brr_id", null, true, ["id"]);

      $observacion = "Barrio ID: " . $rta[0]->brr_id . ". Codigo: " . $this->parametros["codigo"] 
        . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 4, $observacion, true, "I", ""); 
    } else {
      $rta =  $this->actualizarData("UPDATE conf.brr_barrio SET brr_codigo =:codigo, 
      brr_nombre = :nombre, brr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario,
      vlc_id_upz = :upzid, brr_nombre_comun = :nombrecomun, zna_id = :zonaid
      WHERE brr_id = :id", null, true);

      $observacion = "Barrio ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
      . ". Nombre: " . $this->parametros["nombre"];
      $this->insertarAuditoria(Core::$usuarioID, 4, $observacion, true, "M", ""); //Existe el elemento
    }
    return $rta;
  }
//#endregion
}