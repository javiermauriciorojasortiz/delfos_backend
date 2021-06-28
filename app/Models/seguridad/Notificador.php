<?php

namespace App\Models\Seguridad;

use Illuminate\Http\Request;

//Clase de gestión del usuario
class Notificador extends Usuario {

    //Construye el modelo
    function __construct(Request $request, int $opcion) {
      parent::__construct($request, $opcion);
    }
    //Obtener lista notificaciones
    function consultarNotificadores() {
      $rta = $this->obtenerResultset("SELECT distinct usr_id id, u.eus_id estado, u.tid_id tipoidentificacion, u.usr_identificacion identificacion,
            u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
            u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
            u.usr_fecha_activacion fecha_activacion, '' auditoria, u.usr_fecha_intento fecha_intento,
            eus_nombre nombreestado, tid_codigo codigotipoidentificacion 
          FROM seg.usr_usuario u 
          inner join conf.tid_tipo_identificacion t on t.tid_id = u.tid_id
          inner join seg.eus_estado_usuario o on o.eus_id = u.eus_id 
          where (exists(select 1 from seg.rou_rol_usuario r where r.usr_id = u.usr_id and (r.tus_id = :tipousuario)) or :tipousuario = 0)
            and (LOWER(u.usr_primer_nombre || u.usr_primer_apellido) like  '%' || coalesce(LOWER(:nombreusuario),'') || '%')
            and (LOWER(u.usr_email) like '%' || coalesce(LOWER(:emailusuario),'') || '%')
            and usr_fecha_creacion between coalesce(:fechainiusuario, TO_DATE('20000101', 'YYYYMMDD')) and coalesce(:fechafinusuario, TO_DATE('21000101', 'YYYYMMDD'))
            and o.eus_id = coalesce(:estadousuario, o.eus_id) 
            limit 1000", $this->parametros);
      return $rta;
      //$rta = DB::select('exec seg.pausr_obtenerporsesion(?)',array($params));
    }
  //Obtener lista notificaciones
  function obtenerNotificadorPorID() {
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
  public function establecerNotificador(int $usuarioidref){
    $params = $this->parametros;
    $params["usuarioidref"] = $usuarioidref;
    if($this->parametros["id"]== 0) {//Insertar

      return $this->actualizarData("INSERT INTO oper.ntf_notificador(
        ntf_id, ntf_pregrado, ntf_registro_medico, ntf_autoriza_email, ntf_autoriza_sms,
        ntf_fecha_auditoria, usr_id_auditoria, vlc_id_especialidad, usr_id)
        VALUES (nextval('seg.seqntf'), :pregrado, :registromedico, :autorizaemail, :autorizasms, 
          current_timestamp, :usuario, :especialidadid, :usuarioidref)", $params, true);
    } else { //Actualizar
      
      return $this->actualizarData("UPDATE oper.ntf_notificador SET ntf_pregrado = :pregrado, 
          ntf_registro_medico : registromedico, ntf_autoriza_email = :autorizaemail, ntf_autoriza_sms :autorizasms,
          ntf_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario, vlc_id_especialidad = :especialidadid,
          usr_id = :usuarioidref
          WHERE ntf_id =  :id", $params, true);
    }
  }
  //Establece el notificador y retorna el número
  public function validarNotificador(){
      return $this->actualizarData("UPDATE oper.ntf_notificador SET  usr_id_valida = :usuario, 
          ntf_fecha_valida = current_timestamp, ntf_anotacion_valida = :anotacion,
          WHERE ntf_id = :id", null, true);
  }
}