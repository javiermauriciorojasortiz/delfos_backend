<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del catálogo de valores
class EAPB extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar listas de EAPBs - EPSs del sistema
  function consultarEAPBs() {
   return $this->obtenerResultset("SELECT eap_id, eap_codigo codigo, eap_nombre nombre,
                      eap_default pordefecto,
                      usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
                      from conf.eap_eapb e
                      left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
                      where LOWER(eap_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
                      and LOWER(eap_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }
}
