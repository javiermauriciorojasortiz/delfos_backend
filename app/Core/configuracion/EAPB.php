<?php

namespace App\Core\Configuracion;

use App\Mail\msgUsuario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


//Clase de gestión del catálogo de valores
class EAPB {
  function consultarEAPBs($params) {
   return DB::select("SELECT eap_id, eap_codigo codigo, eap_nombre nombre,
                      eap_default pordefecto,
                      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                      from conf.eap_eapb e
                      left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
                      where LOWER(eap_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
                      and LOWER(eap_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'", $params);
  }
}
