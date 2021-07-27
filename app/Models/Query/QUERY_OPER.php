<?php

namespace App\Models\Query;

//Clase de gestión de auditoría
class QUERY_OPER {

  //Consultar caso por identificación
  public const _CSO_CONSULTARXIDENTIFICACION = "SELECT cso.cso_id id, tid_codigo || ' ' || cso_identificacion identificacion, 
    cso_primer_nombre || ' ' || coalesce(cso_primer_apellido, '') nombre,
    CASE WHEN NOT cso_nacido then 'No nacido' 
        ELSE (DATE_PART('month', current_timestamp) - DATE_PART('month', cso_fecha_nacido)) || ' Meses' END edad 
    FROM oper.cso_caso cso INNER JOIN conf.tid_tipo_identificacion tid ON tid.tid_id = cso.tid_id
    WHERE cso.cso_identificacion = :identificacion
    OR EXISTS(SELECT 1 FROM oper.csoh_caso_hst WHERE cso_identificacion = :identificacion)
    OR EXISTS(SELECT 1 FROM oper.rpc_responsable_caso rpc 
            INNER JOIN seg.usr_usuario rps ON rps.usr_id = rpc.rps_id 
            WHERE cso.cso_id = rpc.cso_id AND usr_identificacion = :identificacion)";

  //Consultar caso por id
  public const _CSO_OBTENERXID = "SELECT cso.cso_id id, cso.tid_id tipoidentificacionid, cso_identificacion identificacion,
    cso_primer_nombre primer_nombre, cso_primer_apellido primer_apellido, cso_segundo_nombre segundo_nombre, 
    cso_segundo_apellido segundo_apellido, cso_nacido nacido, cso_fecha_nacido fecha_nacido, cso_semana semana, 
    pai_id paisid, cso_divipol departamento, cso.dvp_id deptoid, mnc.mnc_id municipioid, cso_municipio municipio,
    cso_barrio barrio, brr_id barrioid, cso_direccion direccion, eap_id eapbid, 
    case when not cso_fecha_nacido is null then 1 else 0 end nacido,
    sgm_id_ultimo seguimientoid, 
    usr.usr_primer_nombre || ' ' || usr.usr_primer_apellido || '-' 
                          || to_char(cso.cso_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM')  auditoria,
    cso_activo activo, vlc_id_causal_inactivo causal_inactivo 
    FROM oper.cso_caso cso
    LEFT JOIN conf.mnc_municipio mnc on mnc.mnc_id = cso.mnc_id
    LEFT JOIN seg.usr_usuario usr on usr.usr_id = cso.usr_id_auditoria
    where cso.cso_id = :id";
  
  //Consultar casos por responsable
  public const _CSO_CONSULTARPORRPSID = "SELECT cso.cso_id id, tid_codigo || ' ' || cso_identificacion identificacion, 
        cso_primer_nombre || ' ' || coalesce(cso_primer_apellido, '') nombre,
        CASE WHEN NOT cso_nacido then 'No nacido' 
            ELSE (DATE_PART('month', current_timestamp) - DATE_PART('month', cso_fecha_nacido)) || ' Meses' END edad,
      case when cso_activo then 'Activo' else 'Inactivo - ' || vlc.vlc_nombre end estado
        FROM oper.cso_caso cso INNER JOIN conf.tid_tipo_identificacion tid ON tid.tid_id = cso.tid_id
      INNER JOIN oper.rpc_responsable_caso rpc on rpc.cso_id = cso.cso_id
      LEFT JOIN conf.vlc_valor_catalogo vlc on vlc.vlc_id = cso.vlc_id_causal_inactivo 
      WHERE rpc.rps_id = :id";
  //Consultar contacto
  public const _CTT_OBTENERXID = "SELECT ctt_id id, ctt_nombre nombre, ctt_cargo cargo, ctt_direccion direccion,
    m.mnc_id municipioid, m.dvp_id departamentoid, ctt_telefono telefono, ctt_email email
    from oper.ctt_contacto u left join conf.mnc_municipio m on m.mnc_id = u.mnc_id where u.ctt_id = :id";
  //Insertar contacto
  public const _CTT_INSERTAR = "INSERT INTO oper.ctt_contacto(ctt_id, ctt_nombre, ctt_cargo, ctt_direccion, mnc_id, 
    ctt_telefono, ctt_email) VALUES (nextval('oper.seqctt'), :nombre, :cargo, :direccion, :municipioid, 
    :telefono, :email) RETURNING ctt_id";
  //Actualizar contacto
  public const _CTT_ACTUALIZAR = "UPDATE oper.ctt_contacto SET ctt_nombre = :nombre, ctt_cargo = :cargo, ctt_direccion = :direccion,
    mnc_id = :municipioid, ctt_telefono = :telefono, ctt_email = :email WHERE ctt_id = :id";

  //Eliminar contacto
  public const _CTT_ELIMINAR = "DELETE FROM oper.ctt_contacto where ctt_id = :id";
  //Listar estados paciente
  public const _ESP_LISTAR = "SELECT esp_id id, esp_nombre nombre, vlc_id_nivel_riesgo nivelriesgoid, vlc_nombre nivelriesgo,
    vlc_codigo codigo FROM oper.esp_estado_paciente e inner join conf.vlc_valor_catalogo v on v.vlc_id = e.vlc_id_nivel_riesgo 
    order by 2";
  //Insertar caso
  public const _CSO_INSERTAR = "INSERT INTO oper.cso_caso(cso_id, tid_id, cso_identificacion, cso_primer_nombre, cso_segundo_nombre, cso_primer_apellido, 
      cso_segundo_apellido, cso_fecha_nacido, cso_semana, pai_id, cso_divipol, dvp_id, mnc_id, cso_barrio, brr_id, cso_direccion, 
      eap_id, cso_fecha_auditoria, usr_id_auditoria, cso_nacido, cso_activo, cso_municipio)
    VALUES (nextval('oper.seqcso'), :tipoidentificacionid, :identificacion, :primer_nombre, :segundo_nombre, :primer_apellido,	
      :segundo_apellido, :fecha_nacido, :semana, :paisid, :departamento, :departamentoid, :municipioid, :barrio, :barrioid, :direccion,
      :eapbid, current_timestamp, :usuario, :nacido, true, :municipio) RETURNING cso_id;";
  //Actualizar caso general
  public const _CSO_ACTUALIZAR = "UPDATE oper.cso_caso SET tid_id=:tipoidentificacionid, cso_identificacion=:identificacion, cso_primer_nombre=:primer_nombre, 
    cso_segundo_nombre=:segundo_nombre, cso_primer_apellido=:primer_apellido, cso_segundo_apellido=:segundo_apellido,
    cso_fecha_nacido=:fecha_nacido, cso_semana=:semana,	pai_id=:paisid,	cso_divipol=:departamento, mnc_id=:municipioid, 
    cso_barrio=:barrio, brr_id=:barrioid, cso_direccion=:direccion,	eap_id=:eapbid, cso_fecha_auditoria = current_timestamp,
    dvp_id = :departamentoid, cso_municipio = :municipio,
    usr_id_auditoria=:usuario, cso_nacido=:nacido where cso_id=:id";
}