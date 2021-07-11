<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class EAPB extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de EAPBs - EPSs del sistema
  function consultarEAPBs() {
    $rta = $this->obtenerResultset("SELECT eap_id id, eap_codigo codigo, eap_nombre nombre,
                      eap_por_defecto pordefecto, eap_activa activa, ctt_id_principal contactoprincipalid, ctt_id_secundario contactosecundarioid,
                      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(e.eap_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                      from conf.eap_eapb e
                      left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
                      where LOWER(eap_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
                      and LOWER(eap_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
    $observacion = "Consultar EAPBS";
    $this->insertarAuditoria(Core::$usuarioID,11, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Eliminar la EPAB por id
  function eliminarEAPB() {
    $rta = $this->obtenerResultset("DELETE FROM conf.eap_eapb where eap_id = :id RETURNING eap_id, eap_codigo, eap_nombre"); 
    $observacion = "EAPB ID : " . $this->parametros["id"] . ". Código: " . $rta[0]->eap_codigo . ". Nombre: " . $rta[0]->eap_nombre;
    $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "E", ""); //Existe el usuario
    return $rta;
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB($contactoprincipalid, $contactosecundarioid) {
      $params = $this->parametros;
      $params["contactoprincipalid"] = $contactoprincipalid;
      $params["contactosecundarioid"] = $contactosecundarioid;
      if($this->parametros["id"] == 0){
        $rta = $this->obtenerResultset("INSERT INTO conf.eap_eapb (eap_id, eap_codigo, eap_nombre, eap_por_defecto, ctt_id_principal, ctt_id_secundario, 
              eap_fecha_auditoria, usr_id_auditoria, eap_activa) 
        VALUES (nextval('conf.seqeap'), :codigo, :nombre, :pordefecto, :contactoprincipalid, :contactosecundarioid,
              current_timestamp, :usuario, :activa) RETURNING eap_id", $params, true, ["id","contactoprincipal","contactosecundario"]);
        $observacion = "EAPB ID: " . $rta[0]->eap_id . ". Codigo: " . $this->parametros["codigo"] 
                      . ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "I", ""); //Existe el usuario
    } else {
        $rta = $this->actualizarData("UPDATE conf.eap_eapb SET eap_codigo = :codigo, eap_nombre = :nombre, eap_por_defecto = :pordefecto, 
              ctt_id_principal = :contactoprincipalid, ctt_id_secundario = :contactosecundarioid, eap_activa = :activa,
              eap_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE eap_id = :id", $params, true,
              ["contactoprincipal","contactosecundario"]);
        $observacion = "EAPB ID: " . $this->parametros["id"] . ". Codigo: " . $this->parametros["codigo"] 
              . ". Nombre: " . $this->parametros["nombre"];
        $this->insertarAuditoria(Core::$usuarioID, 11, $observacion, true, "M", ""); //Existe el usuario    }
    }
    return $rta;
  }
}
