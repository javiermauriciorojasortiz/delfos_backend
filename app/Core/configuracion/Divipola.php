<?php

namespace App\Core\Configuracion;

use App\Mail\msgUsuario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


//Clase de gestión del catálogo de valores
class Divipola {
  function consultarDivipolas($params) {
   return DB::select("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
                        pai_nombre paisnombre,
                        dvp_ent_territorial entidadTerritorial,
                        usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                      from conf.dvp_divipola d
                      inner join conf.pai_pais p on p.pai_id = d.pai_id 
                      left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
                      where LOWER(dvp_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
                        and LOWER(dvp_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'", $params);
  }
}