<?php

namespace App\Models\Seguridad;

use Illuminate\Http\Request;

//Clase de gestión del usuario
class Responsable extends Usuario {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }

  //Obtener responsable
  function obtenerResponsablePorID() {
    $rta = $this->obtenerResultset("SELECT ntf_id id, ntf_pregrado pregrado, ntf_registro_medico registromedico,
          ntf_autoriza_email autorizaemail, ntf_autoriza_sms autorizasms, u.usr_id usuarioid, 
          usr_id_valida usuarioidvalida, ntf_fecha_valida fechavalida, ntf_anotacion_valida anotacionvalida, 
          vlc_id_especialidad especialidadid, u.usr_id usuarioid, u.eus_id estado, u.tid_id tipoidentificacion,
          u.usr_identificacion identificacion, u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre,
          u.usr_primer_apellido primer_apellido, u.usr_segundo_apellido segundo_apellido, u.usr_email email,
          u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso, u.usr_fecha_activacion fecha_activacion,
          a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria, 
          v.usr_primer_nombre || ' ' || v.usr_primer_apellido || ' ' || to_char(n.ntf_fecha_valida, 'YYYY-MM-DD HH:MI:SSPM') validacion 
          FROM oper.ntf_notificador n
          inner join seg.usr_usuario u on u.usr_id = n.usr_id
          left join seg.usr_usuario a on a.usr_id = u.usr_id_auditoria
          left join seg.usr_usuario v on v.usr_id = n.usr_id_valida
          left join conf.vlc_valor_catalogo t on t.vlc_id = n.vlc_id_especialidad
          where ntf_id = :id");
    return $rta;
  }
  //Establece el notificador y retorna el número
  public function establecerResponsable(int $usuarioidref){
    $params = $this->parametros;
    $params["usuarioidref"] = $usuarioidref;
    if($this->parametros["id"]== 0) {//Insertar

      return $this->actualizarData("INSERT INTO oper.rps_responsable(
        rps_id, rps_autoriza_email, rps_autoriza_sms, usr_id, rps_fecha_auditoria, usr_id_auditoria)
        VALUES (nextval('seg.seqrps'), :autorizaemail, :autorizasms, :usuarioidref,
          current_timestamp, :usuario)", $params, true);
    } else { //Actualizar
      
      return $this->actualizarData("UPDATE oper.rps_responsable SET 
          rps_autoriza_email = :autorizaemail, rps_autoriza_sms :autorizasms,
          rps_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario,
          usr_id = :usuarioidref
          WHERE rps_id =  :id", $params, true);
    }
  }
}