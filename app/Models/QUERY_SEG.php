<?php

namespace App\Models;

//Clase de gestión de auditoría
class QUERY_SEG {

  //Insertar auditoría
  public const _AUD_INSERTAR = "INSERT INTO seg.aud_auditoria (aud_id, tpa_id, usr_id, aud_descripcion, aud_fecha, 
      aud_observacion, aud_exitoso, vlc_id_operacion)
    SELECT nextval('seg.seqaud'), :tipoauditoria, :usuario, :descripcion, current_timestamp, :observacion,
      :exitoso, conf.fncat_valorseleccionado('OPERACION', :operacion) from seg.tpa_tipo_auditoria 
      where tpa_id = :tipoauditoria and tpa_escribir = CAST(1 as bit)";
  //Consultar auditoria
  public const _AUD_CONSULTAR = "SELECT  aud_id id, aud_fecha fecha, tpa_descripcion tipo, 
      vlc_nombre operacion,
      usr_primer_nombre || ' ' || usr_primer_apellido usuario,
      aud_descripcion descripcion, aud_observacion observacion, aud_exitoso exitoso
      FROM seg.aud_auditoria a inner join seg.usr_usuario u on u.usr_id = a.usr_id
      inner join seg.tpa_tipo_auditoria t on t.tpa_id = a.tpa_id 
      inner join conf.vlc_valor_catalogo v on v.vlc_id = a.vlc_id_operacion
      where t.tpa_id = coalesce(:tipoauditoria, t.tpa_id) and u.usr_identificacion = coalesce(:usuario, u.usr_identificacion)
      and usr_email like coalesce(:email, '') || '%'
      and aud_descripcion like coalesce(:descripcion, '') || '%'
      and aud_fecha >=coalesce(:fechaini, TO_DATE('2000-01-01', 'YYYY-MM-DD')) 
      and aud_fecha <= coalesce(:fechafin, TO_DATE('2100-01-01', 'YYYY-MM-DD'))
      Limit 1000";
  
  //Consultar tipo auditoria
  public const _TPA_CONSULTAR = "SELECT tpa_id id, tpa_descripcion nombre, tpa_escribir escribir from seg.tpa_tipo_auditoria";
  //Validar Session
  public const _SES_VALIDAR = "SELECT seg.fnusr_validarsesion(:sesion, :ip, :opcion)";
  //Consultar tipos de usuario
  public const _TUS_CONSULTAR = "SELECT tus_id id, tus_nombre nombre, vlc_nombre nombretipoentidad, 
    t.vlc_id_tipo_entidad tipoentidadid, vlc_codigo codigotipoentidad
    FROM seg.tus_tipo_usuario t inner join conf.vlc_valor_catalogo v on v.vlc_id = t.vlc_id_tipo_entidad
    WHERE tus_externo = coalesce(:externo, tus_externo)";
  //Insertar usuario
  public const _USR_INSERTAR = "INSERT INTO seg.usr_usuario(usr_id, eus_id, tid_id, usr_identificacion, 
      usr_primer_nombre, usr_segundo_nombre, usr_primer_apellido, usr_segundo_apellido, usr_email, usr_telefonos, 
      usr_fecha_auditoria, usr_id_auditoria, usr_intentos, usr_fecha_creacion)
    VALUES (nextval('seg.sequsr'), :estado, :tipoidentificacionid, :identificacion, 
    :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :email, :telefonos,
      current_timestamp, case when :usuario <=0 then null else :usuario end, 0, current_timestamp) RETURNING usr_id";
  //Actualizar usuario
  public const _USR_ACTUALIZAR = "UPDATE seg.usr_usuario SET eus_id = :estado, tid_id = :tipoidentificacionid,
      usr_identificacion = :identificacion, usr_primer_nombre = :primer_nombre, usr_segundo_nombre = :segundo_nombre, 
      usr_primer_apellido = :primer_apellido, usr_segundo_apellido = :segundo_apellido, usr_email = :email, 
      usr_telefonos = :telefonos, usr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario, 
      usr_intentos = 0, usr_fecha_intento = null
    WHERE usr_id =  :id";
  //Insertar rol Usuario
  public const _USR_INSERTARROL = "INSERT INTO seg.rou_rol_usuario (usr_id, tus_id, rou_entidadid, usr_id_auditoria, rou_fecha_auditoria)
      values(:usuarioid, :tipousuarioid, :entidadrol, :usuario, current_timestamp)";
  //Eliminar rol usuario
  public const _USR_ELIMINARROL = "DELETE FROM seg.rou_rol_usuario where usr_id = :usuarioid 
      and tus_id = :tipousuarioid and rou_entidadid = :entidadid";
  //Obtener usuario por id
  public const _USR_OBTENERPORID = "SELECT u.usr_id  id, u.eus_id estado, u.tid_id tipoidentificacion,
    u.usr_identificacion identificacion, u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre,
    u.usr_primer_apellido primer_apellido, u.usr_segundo_apellido segundo_apellido, u.usr_email email,
    u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso, u.usr_fecha_activacion fecha_activacion,
    a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria 
    from seg.usr_usuario u left join seg.usr_usuario a on a.usr_id = u.usr_id_auditoria 
    where u.usr_id = :id";
  //Cambiar clave
  public const _USR_CAMBIARCLAVE = "SELECT * FROM seg.fnusr_actualizarclave(:id,:claveanterior,:clavenueva, :usuario)";
  //Autenticar por usuario y clave
  public const _USR_AUTENTICAR = 'SELECT * FROM seg.fnusr_autenticar(:tipousuario, :emailidentificacion, :clave, :ip)';
  //Autenticar por token
  public const _USR_AUTENTICARXTOKEN = 'SELECT * FROM seg.fnusr_autenticarportoken(:sesion, :emailidentificacion, :ip)';
  //Consultar usuarios
  public const _USR_CONSULTAR = "SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
      u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
      u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
      u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento, eus_nombre nombreestado, tid_codigo codigotipoidentificacion,
      a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    FROM seg.usr_usuario u 
    inner join conf.tid_tipo_identificacion t on t.tid_id = u.tid_id
    inner join seg.eus_estado_usuario o on o.eus_id = u.eus_id 
    left join seg.usr_usuario a on a.usr_id = u.usr_id_auditoria
    where (exists(select 1 from seg.rou_rol_usuario r where r.usr_id = u.usr_id and (r.tus_id = :tipousuario)) or :tipousuario = 0)
      and (LOWER(u.usr_primer_nombre || u.usr_primer_apellido) like  '%' || coalesce(LOWER(:nombreusuario),'') || '%')
      and (LOWER(u.usr_email) like '%' || coalesce(LOWER(:emailusuario),'') || '%')
      and u.usr_fecha_creacion between coalesce(:fechainiusuario, TO_DATE('20000101', 'YYYYMMDD')) and coalesce(:fechafinusuario, TO_DATE('21000101', 'YYYYMMDD'))
      and o.eus_id = coalesce(:estadousuario, o.eus_id) 
      limit 1000";
  //Iniciar sesión temporal
  public const _SST_INICIAR = 'SELECT * FROM seg.fnsst_iniciar(:emailidentificacion, :tipo,  :ip)';
  //Generar clave aleatoria
  public const _USR_GENERARCLAVEALEATORIA = 'SELECT  * FROM seg.fnusr_generarclavealeatoria(:emailidentificacion, :tipo,  :ip, :clave)';
  //Obtener menú
  public const _USR_OBTENERMENU = "SELECT distinct o.opc_id id, opc_nombre nombre, opc_descripcion descripcion, opc_ruta url, 
      coalesce(opc_id_padre, 0) opcionPadre, opc_orden orden
      FROM seg.rou_rol_usuario r 
      inner join seg.tuo_tipo_usuario_opcion t on t.tus_id = r.tus_id 
      inner join seg.opc_opcion o on o.opc_id = t.opc_id
      where r.usr_id = :usuario
      order by coalesce(opc_id_padre, 0) asc, opc_orden asc";

  //Generar clave aleatoria
  public const _USR_GENERARNUEVACLAVE = "SELECT seg.random_string(10)";
  //Obtener estados
  public const _EUS_CONSULTAR = "SELECT eus_id id, eus_nombre nombre, eus_activo activo FROM seg.eus_estado_usuario";
  //Obtener roles usuario
  public const _USR_OBTENERROL = "SELECT  r.usr_id usuarioid, r.tus_id tipousuarioid, tus_nombre nombretipousuario,
      t.vlc_id_tipo_entidad tipoentidadid, vlc_nombre nombretipoentidad, r.rou_entidadid entidadid,
      coalesce(eap_nombre, upu_nombre, mnc_nombre, '(No requerida)') nombreentidad,
      u.usr_primer_nombre || ' ' || u.usr_primer_apellido || ' ' || to_char(r.rou_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria 
      FROM seg.rou_rol_usuario r
      inner join seg.tus_tipo_usuario t on t.tus_id = r.tus_id
      inner join conf.vlc_valor_catalogo v on v.vlc_id = t.vlc_id_tipo_entidad
      left join seg.usr_usuario u on u.usr_id = r.usr_id_auditoria 
      left join conf.eap_eapb e on e.eap_id = r.rou_entidadid and v.vlc_codigo = 'EAPB'
      left join conf.upu_upgd_ui i on i.upu_id = r.rou_entidadid and v.vlc_codigo = 'UPGD'
      left join conf.mnc_municipio m on m.mnc_id = r.rou_entidadid and v.vlc_codigo = 'SECR'
      where r.usr_id = :id";
  //Eliminar usuario
  public const _USR_ELIMINAR = "DELETE from seg.usr_usuario where usr_id = :id RETURNING usr_identificacion";
  //Inactivar usuario
  public const _USR_INACTIVAR = "UPDATE seg.usr_usuario set eus_id = 3 where usr_id = :id";
  //Consultar notificadores
  public const _NTF_CONSULTAR = "SELECT distinct usr_id id, u.eus_id estado, u.tid_id tipoidentificacion, u.usr_identificacion identificacion,
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
      limit 1000";
  //Obtener notificador por id
  public const _NTF_OBTENERXID = "SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
    u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
    u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
    u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento,
    a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(n.ntf_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
    ntf_pregrado pregrado, ntf_registro_medico registro_medico, ntf_autoriza_email autoriza_email, 
    ntf_autoriza_sms autoriza_sms, vlc_id_especialidad especialidadid
    from seg.usr_usuario u left join oper.ntf_notificador n on n.ntf_id = u.usr_id
    left join seg.usr_usuario a on a.usr_id = n.usr_id_auditoria 
    where u.usr_id = :id";
  //Insertar notificador 
  public const _NTF_INSERTAR = "INSERT INTO oper.ntf_notificador(ntf_id, ntf_pregrado, ntf_registro_medico, ntf_autoriza_email, 
    ntf_autoriza_sms, ntf_fecha_auditoria, usr_id_auditoria, vlc_id_especialidad)
    VALUES (:id, :pregrado, :registro_medico, :autoriza_email, :autoriza_sms, current_timestamp, :usuario, :especialidadid)";
  //Actualizar notificador
  public const _NTF_ACTUALIZAR = "UPDATE oper.ntf_notificador SET ntf_pregrado = :pregrado, ntf_registro_medico = :registro_medico, 
    ntf_autoriza_email = :autoriza_email, ntf_autoriza_sms = :autoriza_sms, ntf_fecha_auditoria = current_timestamp, 
    usr_id_auditoria= :usuario, vlc_id_especialidad = :especialidadid WHERE ntf_id = :id";
  //Validar notificador
  public const _NTF_VALIDAR = "UPDATE oper.ntf_notificador SET  usr_id_valida = :usuario, 
    ntf_fecha_valida = current_timestamp, ntf_anotacion_valida = :anotacion,
    WHERE ntf_id = :id";

}