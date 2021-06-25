<?php

namespace App\Core\Seguridad;

use App\Mail\msgUsuario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
    //Obtiene el usuario si el login es correcto
    function autenticarporToken($params){
      $rta = DB::select('select * from seg.fnusr_autenticarportoken(:sesion, :emailidentificacion, :ip)',$params);
      $this->usuario = $rta;
      return $rta;
    }
    //Envía correo al usuario para que se autentique
    function enviarCorreo($params) {
      $dato = $params["emailidentificacion"];
      $tipousuario = $params["tipousuario"];
      $sqlparams = array("emailidentificacion" => $dato,
                    "tipo" => $params["tipousuario"],
                    "ip" => $params["ip"]);  

      if($params["metodoautenticacion"] == 1 && ($tipousuario == 1 || $tipousuario == 2)) {//usuario nuevo solo puede ser médico o responsable
        $rta = DB::select('select seg.fnsst_iniciar(:emailidentificacion, :tipo,  :ip)',$sqlparams);
        if($rta[0]->fnsst_iniciar == "ESUSUARIO") 
          throw new Exception("El correo pertenece a un usuario registrado. Por favor, contactese con Delfos o seleccione la opción adecuada si se le olvidó la clave");
        if($rta[0]->fnsst_iniciar == "")
          throw new Exception("Se ha intentado generar un nuevo correo desde otra IP. Por seguridad, debe esperar al menos una hora para volver a intentarlo");
        if($rta[0]->fnsst_iniciar  == "5MINUTOS")
          throw new Exception("Por favor, si no ha recibido el correo revise en su bandeja de spam. Por seguridad, espere 5 minutos para volver a intentarlo");
 
          $mensaje["usuario"] = "Esperamos que se encuentre muy bien " . $rta[0]->usuario ;
          $mensaje["texto"] = "Su nueva clave temporal es " . $rta[0]->clave . ". Por favor, ingrese con ella y actualicela";
           //Mail::to($dato)->send(new msgUsuario($mensaje));
      } else if ($params["metodoautenticacion"] == 2) { //Usuario olvidó clave enviar clave provisional
        $rta = DB::select('select * from seg.fnusr_generarclavealeatoria(:emailidentificacion, :tipo,  :ip)',$sqlparams);
        if(count($rta)==0)
          throw new Exception("El usuario no fue encontrado");
        if($rta[0]->clave == "")
          throw new Exception("Al usuario no se le concedió clave porque se le acaba de enviar. Por seguridad, espere 5 minutos para volver a intentarlo");

        $mensaje["usuario"] = "Esperamos que se encuentre muy bien " . $rta[0]->usuario ;
        $mensaje["texto"] = "Su nueva clave temporal es " . $rta[0]->clave . ". Por favor, ingrese con ella y actualicela";
        //Mail::to($dato)->send(new msgUsuario($mensaje));
      } else {
        throw new Exception("Solicitud no válida para la aplicación");
      }
      return true; //$rta[0]->fnsst_iniciar;//"fnsst_iniciar"];
    }
    //Obtener lista de usuarios
    function consultarUsuarios($params) {
      //Request = {tipoUsuario: 0, estadoUsuario: "1", nombreUsuario: "", emailUsuario: "", fechaIniUsuario: "", fechaFinUsuario: ""}
      //$prueba["tipousuario"] = 0;//$params["tipousuario"];
      //$prueba["estadousuario"] = 1;
      $rta = DB::select("SELECT distinct usr_id id, u.eus_id estado, u.tid_id tipoidentificacion, u.usr_identificacion identificacion,
            u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
            u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
            u.usr_fecha_activacion fecha_activacion, '' auditoria, u.usr_fecha_intento fecha_intento,
            eus_nombre nombreestado, tid_codigo codigotipoidentificacion 
          FROM seg.usr_usuario u 
          inner join conf.tid_tipo_identificacion t on t.tid_id = u.tid_id
          inner join seg.eus_estado_usuario o on o.eus_id = u.eus_id 
          where (exists(select 1 from seg.rou_rol_usuario r where r.usr_id = u.usr_id and (r.tus_id = :tipousuario)) or :tipousuario = 0)
            and (LOWER(u.usr_primer_nombre || u.usr_primer_apellido) like  '%' || coalesce(LOWER(:nombreusuario),'') || '%')
            and (LOWER(u.usr_email) like '%' || coalesce(LOWER(:emailusuario),'') || '%')
            and usr_fecha_creacion between coalesce(:fechainiusuario, TO_DATE('20000101', 'YYYYMMDD')) and coalesce(:fechafinusuario, TO_DATE('21000101', 'YYYYMMDD'))
            and o.eus_id = coalesce(:estadousuario, o.eus_id) 
            limit 1000", $params);
      return $rta;
      //$rta = DB::select('exec seg.pausr_obtenerporsesion(?)',array($params));
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
                          where t.tpa_id = coalesce(:tipoauditoria, t.tpa_id) and u.usr_identificacion = coalesce(:usuario, u.usr_identificacion)
                          and usr_email like coalesce(:email, '') || '%'
                          and aud_descripcion like coalesce(:descripcion, '') || '%'
                          and aud_fecha >=coalesce(:fechaini, TO_DATE('2000-01-01', 'YYYY-MM-DD')) 
                          and aud_fecha <= coalesce(:fechafin, TO_DATE('2100-01-01', 'YYYY-MM-DD'))
                          Limit 1000", $params);
      //$rta[0]->observacion = serialize($params);
      return $rta;
    }
}