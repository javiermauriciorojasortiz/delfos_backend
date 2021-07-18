<?php

namespace App\Models;

//Clase de gestión de auditoría
class QUERY_SEG {

  //Consultar caso por identificación
  public const _AUD_INSERTAR = "INSERT INTO seg.aud_auditoria (aud_id, tpa_id, usr_id, aud_descripcion, aud_fecha, 
      aud_observacion, aud_exitoso, vlc_id_operacion)
    SELECT nextval('seg.seqaud'), :tipoauditoria, :usuario, :descripcion, current_timestamp, :observacion,
      :exitoso, conf.fncat_valorseleccionado('OPERACION', :operacion) from seg.tpa_tipo_auditoria 
      where tpa_id = :tipoauditoria and tpa_escribir = CAST(1 as bit)";

  //Validar Session
  public const _SES_VALIDAR = "select seg.fnusr_validarsesion(:sesion, :ip, :opcion)";
}