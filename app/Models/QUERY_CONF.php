<?php

namespace App\Models;

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
  
  public const _VLC_INSERTAR = "INSERT INTO conf.vlc_valor_catalogo ( vlc_id, cat_id, vlc_codigo, vlc_nombre, 
    vlc_activo, vlc_fecha_auditoria, usr_id_auditoria) VALUES (nextval('conf.seqvlc'), :catalogoid, :codigo, :nombre, 
    :activo, current_timestamp, :usuario) RETURNING vlc_id";

  public const _VLC_ACTUALIZAR = "UPDATE conf.vlc_valor_catalogo SET vlc_codigo =:codigo, 
    vlc_nombre = :nombre, vlc_activo = :activo, vlc_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
    WHERE vlc_id = :id";
}