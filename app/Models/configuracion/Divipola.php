<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;


//Clase de gestión del divisiones políticas
class Divipola extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consulta de lista de divipolas
  function consultarDivipolas() {
   return $this->obtenerResultset("SELECT dvp_id id, dvp_codigo codigo, dvp_nombre nombre,
          pai_nombre paisnombre,
          dvp_ent_territorial entidadTerritorial,
          usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
        from conf.dvp_divipola d
        inner join conf.pai_pais p on p.pai_id = d.pai_id 
        left join seg.usr_usuario u on u.usr_id = d.usr_id_auditoria
        where LOWER(dvp_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
          and LOWER(dvp_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }

}