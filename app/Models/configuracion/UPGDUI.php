<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class UPGDUI  extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de UPGDs
  function consultarUPGDUIs() {
   return $this->obtenerResultset("SELECT upu_id, upu_codigo codigo, upu_nombre nombre, upu_activo activo, 
    mnc_codigo || '-' || mnc_nombre municipio, 
    case when upu_esupgd = CAST (1 as bit) then 'UPGD' else 'UI' end esupgd,
    usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(e.upu_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.upu_upgd_ui e
    inner join conf.mnc_municipio m on m.mnc_id = e.mnc_id
    left join conf.cat_catalogo c on lower(c.cat_codigo) = 'subred'
    left join conf.vlc_valor_catalogo v on e.cat_subred = v.vlc_codigo and v.cat_id = c.cat_id
    left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
    where LOWER(upu_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
    and LOWER(upu_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'
    and (upu_esupgd = CAST(:tipo as bit) or :tipo is null)");
  }
}
