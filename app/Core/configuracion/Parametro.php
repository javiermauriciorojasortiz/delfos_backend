<?php

namespace App\Core\Configuracion;

use App\Mail\msgUsuario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

//Clase de gestión del usuario
class Parametro {
  function consultarParametros($params) {
   return DB::select("SELECT prg_id id, prg_codigo codigo, prg_nombre nombre,
                      t.tpd_id,
                      t.tpd_nombre tipodatonombre,
                      prg_valor valor,
                      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                    from conf.prg_parametro_general p
                    inner join conf.tpd_tipo_dato t on t.tpd_id = p.tpd_id 
                    left join seg.usr_usuario u on u.usr_id = p.usr_id_auditoria
                    where LOWER(prg_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
            and LOWER(prg_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'", $params);
  }
}