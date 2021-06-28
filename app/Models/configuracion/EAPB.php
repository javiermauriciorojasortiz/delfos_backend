<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class EAPB extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de EAPBs - EPSs del sistema
  function consultarEAPBs() {
   return $this->obtenerResultset("SELECT eap_id, eap_codigo codigo, eap_nombre nombre,
                      eap_default pordefecto,
                      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                      from conf.eap_eapb e
                      left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
                      where LOWER(eap_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
                      and LOWER(eap_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }
  //Eliminar la EPAB por id
  function eliminarEAPB() {
    return $this->actualizarData("DELETE conf.eap_eapb where eap_id = :id"); 
  }
  //Establece la EPAB y retorna el número
  function establecerEAPB($contactoprincipalid, $contactosecundarioid) {
    $params = $this->parametros;
    $params["contactoprincipalid"] = $contactoprincipalid;
    $params["contactosecundarioid"] = $contactosecundarioid;
    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.eap_eapb (eap_id, eap_codigo, eap_nombre, eap_default, cct_id_principal, cct_id_secundario, 
            eap_fecha_auditoria, usr_id_auditoria) 
      VALUES (nextval('conf.seceap'), :codigo, :nombre, :default, :contactoprincipalid, :contactosecundarioid,
            current_timestamp, :usuario)", $params, true);
    } else {
      return $this->actualizarData("UPDATE conf.eap_eapb SET eap_codigo = :codigo, eap_nombre = :nombre, eap_default = :default, 
            cct_id_principal = :contactoprincipalid, cct_id_secundario = :contactosecundarioid, 
            eap_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE eap_id = :id", $params, true);
    }
  }
}
