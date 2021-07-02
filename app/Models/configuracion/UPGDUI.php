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
   return $this->obtenerResultset("SELECT upu_id id, upu_codigo codigo, upu_nombre nombre, upu_activo activo, 
    mnc_codigo || '-' || mnc_nombre municipio, vlc_nombre subred, v.vlc_id subredid,
    case when upu_esupgd = CAST (1 as bit) then 'UPGD' else 'UI' end tipo, upu_esupgd esupgd, ctt_id_principal contactoprincipalid,
    ctt_id_secundario contactosecundarioid, dvp_id departamentoid, m.mnc_id municipioid,
    usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(e.upu_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
    from conf.upu_upgd_ui e
    inner join conf.mnc_municipio m on m.mnc_id = e.mnc_id
    left join conf.vlc_valor_catalogo v on e.vlc_id_subred = v.vlc_id
    left join seg.usr_usuario u on u.usr_id = e.usr_id_auditoria
    where LOWER(upu_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
    and LOWER(upu_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'
    and (upu_esupgd = CAST(:tipo as bit) or :tipo is null)");
  }
  //Eliminar la UPGDUI por id
  function eliminarUPGDUI() {
    return $this->actualizarData("DELETE FROM conf.upu_upgd_ui where upu_id = :id"); 
  }
  //Establece la UPGDUI y retorna el número
  function establecerUPGDUI($contactoprincipalid, $contactosecundarioid) {
    $params = $this->parametros;
    $params["contactoprincipalid"] = $contactoprincipalid;
    $params["contactosecundarioid"] = $contactosecundarioid;
    if($this->parametros["id"] == 0){
      return $this->actualizarData("INSERT INTO conf.upu_upgd_ui (upu_id, upu_codigo, upu_nombre, upu_esupgd, upu_activo, 
        mnc_id, vlc_id_subred, ctt_id_principal, ctt_id_secundario, upu_fecha_auditoria, usr_id_auditoria)
        VALUES (nextval('conf.sequpu'), :codigo, :nombre, :esupgd, :activo, :municipioid, :subredid, :contactoprincipalid, :contactosecundarioid,
        current_timestamp, :usuario)", $params, true, ["id","contactoprincipal","contactosecundario","departamentoid"]);
    } else {
      return $this->actualizarData("UPDATE conf.upu_upgd_ui SET upu_codigo = :codigo, upu_nombre = :nombre, 
            upu_esupgd = :esupgd, upu_activo = :activo, mnc_id = :municipioid, vlc_id_subred = :subredid,
            ctt_id_principal = :contactoprincipalid, ctt_id_secundario = :contactosecundarioid, 
            upu_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario WHERE upu_id = :id", $params, true,
            ["contactoprincipal","contactosecundario","departamentoid"]);
    }
  }
}
