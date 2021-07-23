<?php

namespace App\Models\Query;

//Clase de lista querys de configuración
class QUERY_CONF {

  public const _TID_CONSULTAR = "SELECT tid_id id, tid_codigo codigo, tid_nombre nombre FROM conf.tid_tipo_identificacion 
    WHERE tid_paciente = coalesce(:paciente, tid_paciente) AND tid_nacional = coalesce(:nacional, tid_nacional)";
  //Consultar catalogos 
  public const _CAT_CONSULTAR = "SELECT cat_id id, cat_codigo codigo, cat_nombre nombre, cat_descripcion descripcion,
      cat_id_padre idpadre, cat_editable editable
    FROM conf.cat_catalogo c
    where LOWER(cat_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
    and LOWER(cat_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'";

  //Valores por código catálogo 
  public const _VLC_LISTARXCATCODIGO = "SELECT vlc_id id, vlc_codigo codigo, vlc_nombre nombre, vlc_activo activo,
    usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(v.vlc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
    vlc_id_padre idpadre
    FROM conf.cat_catalogo c inner join conf.vlc_valor_catalogo v on v.cat_id = c.cat_id
    left join seg.usr_usuario u on u.usr_id = v.usr_id_auditoria
    where LOWER(cat_codigo) = LOWER(:codigo)";
  //Valores por id Catálogo
  public const _VLC_LISTARXCATID = "SELECT vlc_id id, vlc_codigo codigo, vlc_nombre nombre, vlc_activo activo,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(v.vlc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      vlc_id_padre idpadre
      FROM conf.cat_catalogo c inner join conf.vlc_valor_catalogo v on v.cat_id = c.cat_id
      left join seg.usr_usuario u on u.usr_id = v.usr_id_auditoria
      where c.cat_id = :id";
  
  public const _VLC_BORRARXID = "DELETE FROM conf.vlc_valor_catalogo where vlc_id = :id RETURNING vlc_codigo, vlc_nombre";
  //Insertar valor catálogo
  public const _VLC_INSERTAR = "INSERT INTO conf.vlc_valor_catalogo ( vlc_id, cat_id, vlc_codigo, vlc_nombre, 
    vlc_activo, vlc_fecha_auditoria, usr_id_auditoria) VALUES (nextval('conf.seqvlc'), :catalogoid, :codigo, :nombre, 
    :activo, current_timestamp, :usuario) RETURNING vlc_id";
  //Actualizar valor catálogo
  public const _VLC_ACTUALIZAR = "UPDATE conf.vlc_valor_catalogo SET vlc_codigo =:codigo, 
    vlc_nombre = :nombre, vlc_activo = :activo, vlc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
    WHERE vlc_id = :id";
  //Consultar divipola
  public const _DVP_CONSULTAR = "SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
      pai_nombre nombrepais,
      dvp_ent_territorial entidadTerritorial,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.dvp_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.dvp_divipola d
    inner join conf.pai_pais p on p.pai_id = d.pai_id 
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where LOWER(dvp_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
      and LOWER(dvp_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'";
  //Listar divipolas
  public const _DVP_LISTAR = "SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
      pai_id paisid, dvp_ent_territorial entidadTerritorial from conf.dvp_divipola d order by dvp_nombre";
  //Eliminar divipola
  public const _DVP_ELIMINAR = "DELETE  FROM conf.dvp_divipola where dvp_id = :id RETURNING dvp_id, dvp_codigo, dvp_nombre";
  //Insertar divipola
  public const _DVP_INSERTAR = "INSERT INTO conf.dvp_divipola (dvp_id, pai_id, dvp_codigo, dvp_nombre, 
      dvp_ent_territorial, usr_id_auditoria, dvp_fecha_auditoria) 
      VALUES (nextval('conf.seqdvp'), 1, :codigo, :nombre, 
              :entidadterritorial, :usuario, current_timestamp) RETURNING dvp_id";
  //Actualizar divipola
  public const _DVP_ACTUALIZAR = "UPDATE conf.dvp_divipola SET dvp_codigo =:codigo, 
    dvp_nombre = :nombre, dvp_ent_territorial = :entidadterritorial, dvp_fecha_auditoria = current_timestamp, 
    usr_id_auditoria = :usuario WHERE dvp_id = :id";
  //Obtener municipios por divipola
  public const _MNC_OBTENERPORIDDVP = "SELECT mnc_id id, d.dvp_id departamentoid, mnc_nombre nombre,
      mnc_codigo codigo, (mnc_ent_territorial = B'1') entidadTerritorial, dvp_nombre nombredepartamento,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.mnc_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.mnc_municipio d
    inner join conf.dvp_divipola v on v.dvp_id = d.dvp_id
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.dvp_id = :id order by mnc_nombre asc";
  //Insert municipio
  public const _MNC_INSERTAR = "INSERT INTO conf.mnc_municipio (mnc_id, dvp_id, mnc_codigo, mnc_nombre, 
    mnc_ent_territorial, usr_id_auditoria, mnc_fecha_auditoria) 
    VALUES (nextval('conf.seqmnc'), :departamentoid, :codigo, :nombre, 
            coalesce(:entidadterritorial, cast(0 as bit)), :usuario, current_timestamp) RETURNING mnc_id";
  //Actualizar municipio
  public const _MNC_ACTUALIZAR = "UPDATE conf.mnc_municipio SET mnc_codigo =:codigo, 
      mnc_nombre = :nombre, mnc_ent_territorial = coalesce(:entidadterritorial,cast(0 as bit)), 
      mnc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
    WHERE mnc_id = :id";
  //Obtener secretarias municipio
  public const _MNC_OBTENERSECRETARIAS = "SELECT mnc_id id, dvp_id departamentoid, mnc_nombre nombre, mnc_codigo codigo
      FROM conf.mnc_municipio WHERE mnc_ent_territorial = cast(1 as bit) order by mnc_nombre asc";
  //Eliminar municipio
  public const _MNC_ELIMINAR = "DELETE FROM conf.mnc_municipio where mnc_id = :id RETURNING mnc_codigo, mnc_nombre";
  //Obtener zonas por id municipio
  public const _ZNA_OBTENERXMNC = "SELECT zna_id id, mnc_id municipioid, zna_nombre nombre, zna_codigo codigo, 
        usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.zna_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
      FROM conf.zna_zona d
      left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
      where mnc_id = :id";
  //Insertar zona
  public const _ZNA_INSERTAR = "INSERT INTO conf.zna_zona (zna_id, mnc_id, zna_codigo, zna_nombre, 
      usr_id_auditoria, zna_fecha_auditoria) 
      VALUES (nextval('conf.seqzna'), :municipioid, :codigo, :nombre, :usuario, current_timestamp) RETURNING zna_id";
  //Actualizar zona
  public const _ZNA_ACTUALIZAR = "UPDATE conf.zna_zona SET zna_codigo =:codigo, zna_nombre = :nombre, 
      zna_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE zna_id = :id";
  //Eliminar zona
  public const _ZNA_ELIMINAR = "DELETE from conf.zna_zona where zna_id = :id RETURNING zna_id, zna_codigo, zna_nombre";
  //Obtener barrios por zona
  public const _BRR_OBTENERXZNA = "SELECT brr_id id, z.zna_id zonaid, brr_nombre nombre,
      brr_codigo codigo, d.vlc_id_upz upzid, vlc_nombre upznombre, zna_nombre nombrezona, brr_nombre_comun nombrecomun,
      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(d.brr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.brr_barrio d
    inner join conf.zna_zona z on z.zna_id = d.zna_id
    left join conf.vlc_valor_catalogo v on v.vlc_id = vlc_id_upz
    left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
    where d.zna_id = :id";
  //Eliminar barrio
  public const _BRR_ELIMINAR = "DELETE FROM conf.brr_barrio where brr_id = :id RETURNING brr_id, brr_codigo, brr_nombre";
  //Insertar barrio
  public const _BRR_INSERTAR = "INSERT INTO conf.brr_barrio (brr_id, zna_id, brr_codigo, brr_nombre, 
      usr_id_auditoria, brr_fecha_auditoria, vlc_id_upz, brr_nombre_comun) 
      VALUES (nextval('conf.seqbrr'), :zonaid, :codigo, :nombre, 
      :usuario, current_timestamp, :upzid, :nombrecomun) RETURNING brr_id";
  //Actualizar barrio
  public const _BRR_ACTUALIZAR = "UPDATE conf.brr_barrio SET brr_codigo =:codigo, 
      brr_nombre = :nombre, brr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario,
      vlc_id_upz = :upzid, brr_nombre_comun = :nombrecomun, zna_id = :zonaid
      WHERE brr_id = :id";
  //Consultar EAPB
  public const _EAP_CONSULTAR = "SELECT eap_id id, eap_codigo codigo, eap_nombre nombre,
    eap_por_defecto pordefecto, eap_activa activa, ctt_id_principal contactoprincipalid, ctt_id_secundario contactosecundarioid,
    usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(e.eap_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    FROM conf.eap_eapb e
    left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
    where LOWER(eap_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
    and LOWER(eap_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'";
  //Listar EAPB
  public const _EAP_LISTAR = "SELECT eap_id id, eap_nombre nombre from conf.eap_eapb order by 2";
  //Eliminar EAPB
  public const _EAP_ELIMINAR = "DELETE FROM conf.eap_eapb where eap_id = :id RETURNING eap_id, eap_codigo, eap_nombre";
  //Insertar EAPB
  public const _EAP_INSERTAR = "INSERT INTO conf.eap_eapb (eap_id, eap_codigo, eap_nombre, eap_por_defecto, ctt_id_principal, ctt_id_secundario, 
      eap_fecha_auditoria, usr_id_auditoria, eap_activa) 
    VALUES (nextval('conf.seqeap'), :codigo, :nombre, :pordefecto, :contactoprincipalid, :contactosecundarioid,
      current_timestamp, :usuario, :activa) RETURNING eap_id";
  //Actualizar EAPB
  public const _EAP_ACTUALIZAR = "UPDATE conf.eap_eapb SET eap_codigo = :codigo, eap_nombre = :nombre, eap_por_defecto = :pordefecto, 
      ctt_id_principal = :contactoprincipalid, ctt_id_secundario = :contactosecundarioid, eap_activa = :activa,
      eap_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE eap_id = :id";
  //Consultar Parametro General
  public const _PRG_CONSULTAR = "SELECT prg_id id, prg_codigo codigo, prg_nombre nombre,
        t.tpd_id, t.tpd_nombre tipodatonombre, t.tpd_expresion tipodatoexpresion, t.tpd_explicacion tipodatoexplicacion,
        prg_valor valor,
        usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(p.prg_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
      from conf.prg_parametro_general p
      inner join conf.tpd_tipo_dato t on t.tpd_id = p.tpd_id 
      left join seg.usr_usuario u on u.usr_id = p.usr_id_auditoria
      where LOWER(prg_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
      and LOWER(prg_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'";
  //Actualizar parámetro general
  public const _PRG_ACTUALIZAR = "UPDATE conf.prg_parametro_general set prg_valor = :valor, usr_id_auditoria = :usuario,
          prg_fecha_auditoria = current_timestamp where prg_id=:id";
  //Obtener parámetro por código
  public const _PRG_OBTENERXCODIGO = "SELECT prg_id id, prg_codigo codigo, prg_nombre nombre, prg_valor valor 
          from conf.prg_parametro_general p where LOWER(prg_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'";
  //Consultar upgd ui
  public const _UPU_CONSULTAR = "SELECT upu_id id, upu_codigo codigo, upu_nombre nombre, upu_activo activo, 
    mnc_codigo || '-' || mnc_nombre municipio, v.vlc_nombre subred, v.vlc_id subredid, n.vlc_nombre nivel,
    case when upu_esupgd = CAST (1 as bit) then 'UPGD' else 'UI' end tipo, upu_esupgd esupgd, ctt_id_principal contactoprincipalid,
    ctt_id_secundario contactosecundarioid, dvp_id departamentoid, m.mnc_id municipioid, n.vlc_id nivelid, upu_direccion direccion,
    usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(e.upu_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.upu_upgd_ui e
    inner join conf.mnc_municipio m on m.mnc_id = e.mnc_id
    left join conf.vlc_valor_catalogo v on e.vlc_id_subred = v.vlc_id
    left join conf.vlc_valor_catalogo n on e.vlc_id_nivel = n.vlc_id
    left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
    where LOWER(upu_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
    and LOWER(upu_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'
    and (upu_esupgd = CAST(:tipo as bit) or :tipo is null)";
  //Eliminar upgd
  public const _UPU_ELIMINAR = "DELETE FROM conf.upu_upgd_ui where upu_id = :id RETURNING upu_nombre";
  //Insertar upgd
  public const _UPU_INSERTAR = "INSERT INTO conf.upu_upgd_ui (upu_id, upu_codigo, upu_nombre, upu_esupgd, upu_activo, 
    mnc_id, vlc_id_subred, ctt_id_principal, ctt_id_secundario, upu_fecha_auditoria, usr_id_auditoria, upu_direccion, vlc_id_nivel)
    VALUES (nextval('conf.sequpu'), :codigo, :nombre, :esupgd, :activo, :municipioid, :subredid, :contactoprincipalid, 
    :contactosecundarioid, current_timestamp, :usuario, :direccion, :nivelid) RETURNING upu_id";
  //Actualizar upgd
  public const _UPU_ACTUALIZAR = "UPDATE conf.upu_upgd_ui SET upu_codigo = :codigo, upu_nombre = :nombre, 
    upu_esupgd = :esupgd, upu_activo = :activo, mnc_id = :municipioid, vlc_id_subred = :subredid,
    ctt_id_principal = :contactoprincipalid, ctt_id_secundario = :contactosecundarioid, 
    upu_direccion = :direccion, vlc_id_nivel = :nivelid,
    upu_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE upu_id = :id";

  //Listar UPGDs disponibles
  public const _UPU_LISTAR = "SELECT upu_id id, upu_nombre nombre from conf.upu_upgd_ui order by 2";
}