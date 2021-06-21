<?php

namespace App\Core\Seguridad;

use Exception;
use Illuminate\Support\Facades\DB;

//Clase de gestión del usuario
class Usuario {
    //Información del usuario autenticado
    public $usuario;
    //Obtiene la información de usuario de la base de datos
    function __construct(string $sesion = null) {
      //Consultar en la base de datos
      if($sesion != null ) {
        $rta = DB::select('select usr_id id from seg.ses_sesion where ses_id = ?', array($sesion));
        if (count($rta) == 0) throw new Exception("Sesión no activa");
        $this->usuario = $rta[0];
      }
    }
    //Obtiene el usuario si el login es correcto
    function autenticar($params){
      $rta = DB::select('select * from seg.fnusr_autenticar(:tipousuario, :emailidentificacion, :clave, :ip)',$params);
      $this->usuario = $rta;
      return $rta;
    }

    //Obtener lista de usuarios
    function consultarUsuarios($params) {
      $rta = DB::select('exec seg.pausr_obtenerporsesion(?)',array($params));

    }
    //Obtener menú del usuario autenticado
    function obtenerMenuUsuario() {
      $rta = DB::select("SELECT distinct o.opc_id id, opc_nombre nombre, opc_descripcion descripcion, opc_ruta url, 
                          coalesce(opc_id_padre, 0) opcionPadre, opc_orden orden
                          FROM seg.rou_rol_usuario r 
                          inner join seg.tuo_tipo_usuario_opcion t on t.tus_id = r.tus_id 
                          inner join seg.opc_opcion o on o.opc_id = t.opc_id
                          where r.usr_id = ?
                          order by coalesce(opc_id_padre, 0) asc, opc_orden asc", array($this->usuario->id));
      return $rta;
    }
    //Consultar auditoría
    function consultarAuditoria($params) {
      $rta = DB::select("SELECT  aud_id id, aud_fecha fecha, tpa_descripcion tipo, 
                          usr_primer_nombre || ' ' || usr_primer_apellido usuario,
                          aud_descripcion descripcion, aud_observacion observacion
                          from seg.aud_auditoria a inner join seg.usr_usuario u on u.usr_id = a.usr_id
                          inner join seg.tpa_tipo_auditoria t on t.tpa_id = a.tpa_id 
                          where t.tpa_id = coalesce(:tipoAuditoria, t.tpa_id) and u.usr_identificacion = coalesce(:usuario, u.usr_identificacion)
                          and usr_email like coalesce(:email, '') || '%'
                          and aud_descripcion like coalesce(:descripcion, '') || '%'
                          and aud_fecha >=coalesce(:fechaIni, TO_DATE('2000-01-01', 'YYYY-MM-DD')) 
                          and aud_fecha <= coalesce(:fechaFin, TO_DATE('2100-01-01', 'YYYY-MM-DD'))
                          Limit 1000", $params);
      //$rta[0]->observacion = serialize($params);
      return $rta;
    }
}