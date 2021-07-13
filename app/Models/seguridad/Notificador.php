<?php

namespace App\Models\Seguridad;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Notificador extends Core {

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

    //Obtener usuario por id
    public function obtenerNotificador(){
      return $this->obtenerResultset("SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
        u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
        u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
        u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento,
        a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(n.ntf_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
        ntf_pregrado pregrado, ntf_registro_medico registro_medico, ntf_autoriza_email autoriza_email, 
        ntf_autoriza_sms autoriza_sms, vlc_id_especialidad especialidadid
        from seg.usr_usuario u left join oper.ntf_notificador n on n.ntf_id = u.usr_id
        left join seg.usr_usuario a on a.usr_id = n.usr_id_auditoria 
        where u.usr_id = :id")[0];
  }
    //Inserta o actualiza el notificador
    public function establecerNotificador(int $id, bool $nuevo){
      $params = array();
      $params["id"] = $id;
      $params["pregrado"] = $this->parametros["pregrado"];
      $params["registro_medico"] = $this->parametros["registro_medico"];
      $params["autoriza_email"] = $this->parametros["autoriza_email"];
      $params["autoriza_sms"] = $this->parametros["autoriza_sms"];
      $params["especialidadid"] = $this->parametros["especialidadid"];
      if($nuevo) {//Insertar
        //throw new Exception(implode("|", $params));
        $rta = $this->actualizarData("INSERT INTO oper.ntf_notificador(ntf_id, ntf_pregrado, ntf_registro_medico, ntf_autoriza_email, 
          ntf_autoriza_sms, ntf_fecha_auditoria, usr_id_auditoria, vlc_id_especialidad)
          VALUES (:id, :pregrado, :registro_medico, :autoriza_email, :autoriza_sms, current_timestamp, :usuario, :especialidadid)", 
          $params, true);
        $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
        $this->insertarAuditoria(Core::$usuarioID,6, $observacion, true, "I", ""); //Existe el usuario
      } else { //Actualizar
        
        $rta = $this->actualizarData("UPDATE oper.ntf_notificador SET ntf_pregrado = :pregrado, ntf_registro_medico = :registro_medico, 
          ntf_autoriza_email = :autoriza_email, ntf_autoriza_sms = :autoriza_sms, ntf_fecha_auditoria = current_timestamp, 
          usr_id_auditoria= :usuario, vlc_id_especialidad = :especialidadid WHERE ntf_id = :id", 
          $params, true);
        $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
        $this->insertarAuditoria(Core::$usuarioID,6, $observacion, true, "M", ""); //Existe el usuario  
      }
      return $rta;
    }

  //Establece el notificador y retorna el número
  public function validarNotificador(){
      return $this->actualizarData("UPDATE oper.ntf_notificador SET  usr_id_valida = :usuario, 
          ntf_fecha_valida = current_timestamp, ntf_anotacion_valida = :anotacion,
          WHERE ntf_id = :id", null, true);
  }
}