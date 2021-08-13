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
  //Insertar diagnostico
  public const _DGN_INSERTAR = "INSERT into oper.dgn_diagnostico (dgn_id, ntf_id, vlc_id_tipo_defecto,
    vlc_id_cardiopatia, vlc_id_tipo_defecto_otro, vlc_id_diagnostico_principal, vlc_id_diagnostico_secundario,
    dgn_fecha, dgn_fecha_auditoria, usr_id_auditoria) 
    VALUES(nextval('oper.seqdgn'),:notificadorid, :tipodefectoid,:cardiopatiaid,:tipodefectootroid,:diagnosticoprincipalid,
      :diagnosticosecundarioid, :fecha, current_timestamp, :usuario) RETURNING dng_id;";
  //Actualizar diagnostico
  public const _DGN_ACTUALIZAR = "UPDATE oper.dgn_diagnostico SET 
    ntf_id=:notificadorid, vlc_id_tipo_defecto=:tipodefectoid, vlc_id_cardiopatia=:cardiopatiaid,
    vlc_id_tipo_defecto_otro=:tipodefectootroid, vlc_id_diagnostico_principal=:diagnosticoprincipalid,
    vlc_id_diagnostico_secundario=:diagnosticosecundarioid, dgn_fecha=fecha,
    usr_id_auditoria=:usuario WHERE dgn_id=:id";
  //Insertar seguimiento
  public const _SGM_INSERTAR = "INSERT into oper.sgm_seguimiento (sgm_id,cso_id,ntf_id,pxe_id_origen,
    vlc_id_tipo_atencion,sgm_fecha,mnc_id,upu_id,sgm_situacion, dgn_id, usr_id_creacion) 
    VALUES (nextval('oper.seqsgm'),:casoid,:notificadorid,
    :proximaevaluacionid,:tipoatencionid,current_timestamp,:municipioid,:upgduiid,:situacion, :usuario) RETURNING sgm_id;";
  //Actualizar seguimiento
  public const _SGM_ACTUALIZAR_VALORACION = "UPDATE oper.sgm_seguimiento SET vlc_id_nivel_satisfaccion=:nivelsatisfaccionid,
    sgm_desc_valoracion=:descvaloracion, rps_id_valoracion=:idvaloracion WHERE sgm_id=:id";
  //Lista de seguimientos caso
  public const _SGM_LISTARXCASO = "SELECT sgm_id id, usr_primer_nombre || ' ' || usr_primer_apellido doctor,
    sgm_fecha fecha, t.vlc_nombre tipoatencion, d.vlc_nombre diagnostico, e.esp_nombre estado
    from oper.sgm_seguimiento s
    inner join seg.usr_usuario u on u.usr_id = s.ntf_id
    inner join conf.vlc_valor_catalogo t on t.vlc_id = s.vlc_id_tipo_atencion
    inner join oper.dgn_diagnostico dgn on dgn.dgn_id = s.dgn_id
    inner join oper.esp_estado_paciente e on e.esp_id = dgn.esp_id
    inner join conf.vlc_valor_catalogo d on t.vlc_id = dgn.vlc_id_diagnostico_principal where cso_id = :casoid";
  //Consultar caso histórico 
  public const _CSOH_LISTAR = "SELECT cso.csoh_id id, tid_codigo || ' ' || cso_identificacion identificacion, 
    cso_primer_nombre || ' ' || coalesce(cso_primer_apellido, '') nombre,
    CASE WHEN NOT cso_nacido then 'No nacido' 
        ELSE (DATE_PART('month', current_timestamp) - DATE_PART('month', cso_fecha_nacido)) || ' Meses' END edad,
      csoh_fecha fecha
    FROM oper.csoh_caso_hst cso INNER JOIN conf.tid_tipo_identificacion tid ON tid.tid_id = cso.tid_id
      WHERE cso.cso_id = :casoid";
  //Consultar solicitudes atención caso
  public const _ATP_LISTARXCASO = "SELECT atp_id id, vlcta.vlc_nombre tipoayuda, atp_descripcion descripcion, 
        atp_fecha fecha, atp_fecha_confirmacion confirmada, vlcns.vlc_nombre nivelsatisfaccion
        FROM oper.atp_atencion_pendiente atp
      INNER JOIN conf.vlc_valor_catalogo vlcta on vlcta.vlc_id = atp.vlc_id_tipo_ayuda 
      LEFT JOIN conf.vlc_valor_catalogo vlcns on vlcns.vlc_id = atp.vlc_id_nivel_satisfaccion 
          WHERE cso_id = :casoid";
  //Insertar Solicitud Atención
  public const _ATP_CREAR = "INSERT INTO oper.atp_atencion_pendiente(atp_id, cso_id, rsp_id, atp_fecha, 
      atp_descripcion, vlc_id_tipo_ayuda) VALUES (nextval('oper.seqatp'), :casoid, :usuario, current_timestamp, 
      :descripcion, :tipoayudaid) RETURNING atp_id";
  //Insertar atención por parte de la EAPB
  public const _ATP_ESTABLECERATENCION = "UPDATE oper.atp_atencion_pendiente SET 
    usr_id_eapb=:usuario, atp_fecha_eapb=:fechaeapb, atp_observacion_eapb=:observacioneapb,
    eap_id = :eapbid, upu_id = :upgduiid, vlc_id_solucion_atencion=:solucionatencionid, 
    atp_fecha_eapb_auditoria = current_timestamp
   WHERE atp_id = :id";
  //Establecer confirmación de la atención recibida
  public const _ATP_ESTABLECERCONFIRMACION = "UPDATE oper.atp_atencion_pendiente SET 
    usr_id_confirmacion=:usuario, atp_fecha_confirmacion=current_timestamp,
    atp_observacion_confirmacion=:observacionconfirmacion, vlc_id_nivel_satisfaccion=:nivelsatisfaccionid
    WHERE atp_id = :id";
  //Listar tareas pendientes
  public const _TAR_LISTAR = "SELECT atp_id id, vtipo.vlc_id tipotareaid, vtipo.vlc_nombre tipotarea, vlc.vlc_codigo alarma, 'Paciente' atenciona, 
    tid_codigo || '-' || cso_identificacion || ' ' || cso_primer_nombre || coalesce(' '  || cso_primer_apellido,'') dirigidoa,
    atp.atp_fecha fecha, dgn.esp_id estadopacienteid, vlc_id_nivel_riesgo nivelriesgoid, vlc.vlc_id alarmaid
    FROM oper.atp_atencion_pendiente atp
    inner join oper.cso_caso cso on cso.cso_id = atp.cso_id
    inner join conf.tid_tipo_identificacion tid on tid.tid_id = cso.tid_id
    inner join conf.vlc_valor_catalogo vlc on vlc.vlc_id = 45 and vlc.cat_id = 13
    inner join conf.vlc_valor_catalogo vtipo on vtipo.vlc_id = 51 and vtipo.cat_id = 14
    inner join seg.rou_rol_usuario rou on rou.tus_id = 6 and rou_entidadid = cso.eap_id
    left join oper.dgn_diagnostico dgn on dgn.dgn_id = cso.dgn_id
    left join oper.esp_estado_paciente esp on esp.esp_id = dgn.esp_id
    where atp_fecha_eapb is null and rou.usr_id = :usuario
    union all
    SELECT atp_id id, vtipo.vlc_id tipotareaid, vtipo.vlc_nombre tipotarea, vlc.vlc_codigo alarma, 'Paciente' atenciona, 
      tid_codigo || '-' || cso_identificacion || ' ' || cso_primer_nombre || coalesce(' '  || cso_primer_apellido,'') dirigidoa,
      atp.atp_fecha fecha, dgn.esp_id estadopacienteid, vlc_id_nivel_riesgo nivelriesgoid, vlc.vlc_id alarmaid
      FROM oper.atp_atencion_pendiente atp
      inner join oper.cso_caso cso on cso.cso_id = atp.cso_id
      inner join conf.tid_tipo_identificacion tid on tid.tid_id = cso.tid_id
      inner join conf.vlc_valor_catalogo vlc on vlc.vlc_id = 45 and vlc.cat_id = 13
      inner join conf.vlc_valor_catalogo vtipo on vtipo.vlc_id = 52 and vtipo.cat_id = 14
      inner join oper.rpc_responsable_caso rpc on rpc.cso_id = cso.cso_id
      left join oper.dgn_diagnostico dgn on dgn.dgn_id = cso.dgn_id
      left join oper.esp_estado_paciente esp on esp.esp_id = dgn.esp_id
      where (not atp_fecha_eapb is null) and atp_fecha_confirmacion is null and rpc_activo = true and rpc.rps_id = :usuario
    order by 7 asc";
  //Obtener solicitud atención por id
  public const _ATP_OBTENERXID = "SELECT atp_id id, c.cso_id casoid, rsp_id responsableid, atp_fecha fecha, atp_descripcion descripcion,
    usr_id_eapb usuarioeapbid, atp_fecha_eapb fechaeapb, atp_observacion_eapb observacioneapb,
    usr_id_confirmacion usuarioconfirmacionid, atp_fecha_confirmacion fechaconfirmacion,
    atp_observacion_confirmacion observacionconfirmacion, vlc_id_tipo_ayuda tipoayudaid,
    vlc_id_nivel_satisfaccion nivelsatisfaccionid, vlc_id_solucion_atencion solucionatencionid,
    e.eap_id eapbid, eap_nombre eapb, upu.upu_id upgdid, upu_nombre upgdui,
    rsp.usr_primer_nombre || ' ' || rsp.usr_primer_apellido || ' ' || to_char(atp_fecha, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
    ueapb.usr_primer_nombre || ' ' || ueapb.usr_primer_apellido || ' ' || to_char(atp_fecha_eapb_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoriaatencion,
    ucon.usr_primer_nombre || ' ' || ucon.usr_primer_apellido || ' ' || to_char(atp_fecha_confirmacion, 'YYYY-MM-DD HH:MI:SSPM') auditoriaconfirmacion
    FROM oper.atp_atencion_pendiente a inner join oper.cso_caso c on c.cso_id = a.cso_id
    inner join conf.eap_eapb e on e.eap_id = coalesce(a.eap_id, c.eap_id)
    inner join seg.usr_usuario rsp on rsp.usr_id = a.rsp_id
    left join seg.usr_usuario ueapb on ueapb.usr_id = a.usr_id_eapb
    left join seg.usr_usuario ucon on ucon.usr_id = a.usr_id_confirmacion
    left join conf.upu_upgd_ui upu on upu.upu_id = a.upu_id
    WHERE atp_id = :id";
}