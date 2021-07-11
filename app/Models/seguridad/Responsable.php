<?php

namespace App\Models\Seguridad;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Responsable extends Core {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Inserta o actualiza al responsable
  public function establecerResponsable(int $id, bool $nuevo){
    $params = array();
    $params["id"] = $id;
    $params["autoriza_email"] = $this->parametros["autoriza_email"];
    $params["autoriza_sms"] = $this->parametros["autoriza_sms"];
    $rta = null;
    if($nuevo) {//Insertar

      $rta =  $this->actualizarData("INSERT INTO oper.rps_responsable(rps_id, rps_autoriza_email, rps_autoriza_sms,  
          rps_fecha_auditoria, usr_id_auditoria)
          VALUES (:id, :autoriza_email, :autoriza_sms, current_timestamp, :usuario)", $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria(Core::$usuarioID,7, $observacion, true, "I", ""); //Existe el usuario  
    } else { //Actualizar
      
      $rta =  $this->actualizarData("UPDATE oper.rps_responsable SET rps_autoriza_email = :autoriza_email, 
          rps_autoriza_sms = :autoriza_sms, rps_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
          WHERE rps_id = :id", $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria(Core::$usuarioID,7, $observacion, true, "M", ""); //Existe el usuario  
    }
    return $rta;
  }
  //Obtener usuario por id
  public function obtenerResponsable(){
    return $this->obtenerResultset("SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
      u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
      u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
      u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento,
      a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(n.rps_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      rps_autoriza_email autoriza_email, rps_autoriza_sms autoriza_sms
      from seg.usr_usuario u left join oper.rps_responsable n on n.rps_id = u.usr_id
      left join seg.usr_usuario a on a.usr_id = n.usr_id_auditoria
      where u.usr_id = :id")[0];
  }
}