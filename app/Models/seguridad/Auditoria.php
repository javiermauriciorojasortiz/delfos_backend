<?php

namespace App\Models\Seguridad;

use App\Models\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase de gestión de auditoría
class Auditoria extends Core {
  //Variable de usuario
  public $usuario;

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar auditoría
  function consultarAuditoria() {
    $rta = $this->obtenerResultset("SELECT  aud_id id, aud_fecha fecha, tpa_descripcion tipo, 
                        vlc_nombre operacion,
                        usr_primer_nombre || ' ' || usr_primer_apellido usuario,
                        aud_descripcion descripcion, aud_observacion observacion, aud_exitoso exitoso
                        from seg.aud_auditoria a inner join seg.usr_usuario u on u.usr_id = a.usr_id
                        inner join seg.tpa_tipo_auditoria t on t.tpa_id = a.tpa_id 
                        inner join conf.vlc_valor_catalogo v on v.vlc_id = a.vlc_id_operacion
                        where t.tpa_id = coalesce(:tipoauditoria, t.tpa_id) and u.usr_identificacion = coalesce(:usuario, u.usr_identificacion)
                        and usr_email like coalesce(:email, '') || '%'
                        and aud_descripcion like coalesce(:descripcion, '') || '%'
                        and aud_fecha >=coalesce(:fechaini, TO_DATE('2000-01-01', 'YYYY-MM-DD')) 
                        and aud_fecha <= coalesce(:fechafin, TO_DATE('2100-01-01', 'YYYY-MM-DD'))
                        Limit 1000", $this->parametros);
    //$rta[0]->observacion = serialize($params);
    return $rta;
  }
  //Obtener tipos de auditoría
  function obtenerTiposAuditoria() {
    $rta = $this->obtenerResultset("SELECT tpa_id id, tpa_descripcion nombre, tpa_escribir escribir from seg.tpa_tipo_auditoria");
    return $rta;
  }
  //Insertar auditoría desde cualquier operación realizada
  function insertar(int $tipoAuditoria, string $descripcion, bool $exitoso, string $operacion, 
                    string $observacion = null) : int{
    $params = array (
      "tipoauditoria" => $tipoAuditoria,
      "descripcion" => $descripcion,
      "observacion" => $observacion,
      "usuario" => $this->usuarioID,
      "exitoso" => $exitoso,
      "operacion" => $operacion
    );
    return DB::update("INSERT INTO seg.aud_auditoria (aud_id, tpa_id, usr_id, aud_descripcion, aud_fecha, 
      aud_observacion, aud_exitoso, vlc_id_operacion)
      SELECT nextval('seg.seqaud'), :tipoauditoria, :usuario, :descripcion, current_timestamp, :observacion,
        :exitoso, conf.fncat_valorseleccionado('OPERACION', :operacion) from seg.tpa_tipo_auditoria 
        where tpa_id = :tipoauditoria and tpa_escribir = CAST(1 as bit)",$params);
  }
}
