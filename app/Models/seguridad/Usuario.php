<?php

namespace App\Models\Seguridad;

use App\Models\Configuracion\Parametro;
use App\Models\Core;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase de gestión del usuario
class Usuario extends Core {
    //Variable de usuario
    public $usuario;

    //Construye el modelo
    function __construct(Request $request, int $opcion) {
      parent::__construct($request, $opcion);
    }
    //Obtiene el usuario si el login es correcto
    function autenticar(bool $novalidar = false){
      $params = $this->parametros;
      if($novalidar) {//No validar el tipo de usuario
        $params["tipousuario"] = -1; 
        $params["clave"] = $params["claveanterior"];
        unset($params["claveanterior"]);//Eliminar esta llave
        unset($params["clavenueva"]);//Eliminar esta llave
        unset($params["confirmarclave"]);//Eliminar esta llave
        unset($params["id"]);//Eliminar esta llave
      } 
      $params["ip"] = $this->variablesServidor["ip"];
      $params["clave"] = $this->encriptarClave($params["clave"]);
      //throw new Exception ($params["clave"]);//implode("|",$params));
      $rta = DB::select('SELECT * FROM seg.fnusr_autenticar(:tipousuario, :emailidentificacion, :clave, :ip)', $params);
      
      if(count($rta) > 0) {
        $observacion = "Estado " . $rta[0]->estadonombre;
        $this->insertarAuditoria($rta[0]->id, 1, "Autenticación por clave", true, "G", $observacion); //Existe el usuario
      }
      return $rta;
    }
    //Obtiene el usuario si el login es correcto
    function autenticarporToken(){
      $params = $this->parametros;
      $params["ip"] = $this->variablesServidor["ip"];
      $rta = DB::select('select * from seg.fnusr_autenticarportoken(:sesion, :emailidentificacion, :ip)', $params);
      $this->usuario = $rta[0];
      return $rta;
    }
    //Envía correo al usuario para que se autentique
    function enviarCorreo() {
      $dato = $this->parametros["emailidentificacion"];
      $tipousuario = $this->parametros["tipousuario"];
      $sqlparams = array("emailidentificacion" => $dato,
                    "tipo" => $this->parametros["tipousuario"],
                    "ip" => $this->parametros["ip"]);  

      if($this->parametros["metodoautenticacion"] == 1 && ($tipousuario == 1 || $tipousuario == 2)) {//usuario nuevo solo puede ser médico o responsable
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
      } else if ($this->parametros["metodoautenticacion"] == 2) { //Usuario olvidó clave enviar clave provisional
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
    function consultarUsuarios() {
      $rta = $this->obtenerResultset("SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
            u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
            u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
            u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento, eus_nombre nombreestado, tid_codigo codigotipoidentificacion,
            a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
          FROM seg.usr_usuario u 
          inner join conf.tid_tipo_identificacion t on t.tid_id = u.tid_id
          inner join seg.eus_estado_usuario o on o.eus_id = u.eus_id 
          left join seg.usr_usuario a on a.usr_id = u.usr_id_auditoria
          where (exists(select 1 from seg.rou_rol_usuario r where r.usr_id = u.usr_id and (r.tus_id = :tipousuario)) or :tipousuario = 0)
            and (LOWER(u.usr_primer_nombre || u.usr_primer_apellido) like  '%' || coalesce(LOWER(:nombreusuario),'') || '%')
            and (LOWER(u.usr_email) like '%' || coalesce(LOWER(:emailusuario),'') || '%')
            and u.usr_fecha_creacion between coalesce(:fechainiusuario, TO_DATE('20000101', 'YYYYMMDD')) and coalesce(:fechafinusuario, TO_DATE('21000101', 'YYYYMMDD'))
            and o.eus_id = coalesce(:estadousuario, o.eus_id) 
            limit 1000");
      return $rta;
      //$rta = DB::select('exec seg.pausr_obtenerporsesion(?)',array($params));
    }
    //Obtener menú del usuario autenticado
    function obtenerMenuUsuario() {
      $rta = $this->obtenerResultset("SELECT distinct o.opc_id id, opc_nombre nombre, opc_descripcion descripcion, opc_ruta url, 
                          coalesce(opc_id_padre, 0) opcionPadre, opc_orden orden
                          FROM seg.rou_rol_usuario r 
                          inner join seg.tuo_tipo_usuario_opcion t on t.tus_id = r.tus_id 
                          inner join seg.opc_opcion o on o.opc_id = t.opc_id
                          where r.usr_id = :usuario
                          order by coalesce(opc_id_padre, 0) asc, opc_orden asc", null, true);
      return $rta;
    }
    //Eliminar usuario
    public function eliminarUsuario(){
      $rta = 0;
      try {
        $this->actualizarData("DELETE from seg.usr_usuario where usr_id = :id", $this->parametros);
        $rta = 1;
      } catch(\Exception $ex){ //Si el usuario ya tiene referencias se inactiva
        $rta = 2;
        $this->actualizarData("UPDATE seg.usr_usuario set eus_id = 3 where usr_id = :id", $this->parametros);
      }
      return $rta;
    }
    //Encripción de clave
    private function encriptarClave(string $clave) {
      $ciphering = "AES-128-CTR";
      $iv_size = openssl_cipher_iv_length($ciphering);
      $options = 0;
      $encryption_iv = '1234567891011121';
      $encryption_key = "DelfosApp";
      $encryption = openssl_encrypt($clave, $ciphering, $encryption_key, $options, $encryption_iv);
      return $encryption;
    }
    //Cambiar Clave. Retorna 0 si es exitosa o el número de claves no repetidas
    public function cambiarClave(){
      $params = $this->parametros;
      $params["claveanterior"] = $this->encriptarClave($params["claveanterior"]);
      $params["clavenueva"] = $this->encriptarClave($params["clavenueva"]);
      //throw new Exception(implode("|", $params));
      $rta = $this->obtenerResultset("SELECT * FROM seg.fnusr_actualizarclave(:id,:claveanterior,:clavenueva, :usuario)", 
              $params, true, ["confirmarclave","emailidentificacion"]);
      return $rta[0]->fnusr_actualizarclave;
    }

    //Obtener usuario por id
    public function obtenerUsuarioporID(){
        $rta = $this->obtenerResultset("SELECT u.usr_id  id, u.eus_id estado, u.tid_id tipoidentificacion,
        u.usr_identificacion identificacion, u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre,
        u.usr_primer_apellido primer_apellido, u.usr_segundo_apellido segundo_apellido, u.usr_email email,
        u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso, u.usr_fecha_activacion fecha_activacion,
        a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(u.usr_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria 
        from seg.usr_usuario u left join seg.usr_usuario a on a.usr_id = u.usr_id_auditoria 
        where u.usr_id = :id");
    }
    //Establece el usuario y retorna el número
    public function establecerUsuario(array $params = null){

      if($this->parametros["id"]== 0) {//Insertar

        return $this->obtenerResultset("INSERT INTO seg.usr_usuario(usr_id, eus_id, tid_id, usr_identificacion, 
            usr_primer_nombre, usr_segundo_nombre, usr_primer_apellido, usr_segundo_apellido, usr_email, usr_telefonos, 
            usr_fecha_auditoria, usr_id_auditoria, usr_intentos, usr_fecha_creacion)
          VALUES (nextval('seg.sequsr'), 0, :tipoidentificacionid, :identificacion, 
          :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :email, :telefonos,
            current_timestamp, :usuario, 0, current_timestamp) RETURNING usr_id", 
            $params, true, ["estado","auditoria","id", "clave"])[0]->usr_id;
      } else { //Actualizar
        //throw new Exception(implode("|", array_keys($params)));
         $this->actualizarData("UPDATE seg.usr_usuario SET eus_id = :estado, tid_id = :tipoidentificacionid,
            usr_identificacion = :identificacion, usr_primer_nombre = :primer_nombre, usr_segundo_nombre = :segundo_nombre, 
            usr_primer_apellido = :primer_apellido, usr_segundo_apellido = :segundo_apellido, usr_email = :email, 
            usr_telefonos = :telefonos, usr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario, 
            usr_intentos = 0, usr_fecha_intento = null
            WHERE usr_id =  :id", $params, true, ["clave","auditoria"]);
         return $this->parametros["id"];
      }
    }
    //Obtiene la lista de roles posibles de la base de datos
    public function obtenerTiposUsuario() {
      return $this->obtenerResultset("SELECT tus_id id, tus_nombre nombre, vlc_nombre nombretipoentidad, 
      t.vlc_id_tipo_entidad tipoentidadid, vlc_codigo codigotipoentidad
      FROM seg.tus_tipo_usuario t inner join conf.vlc_valor_catalogo v on v.vlc_id = t.vlc_id_tipo_entidad");   
    }
    //Obtiene la lista de estados del usuario
    public function obtenerEstadosUsuario(){
      return $this->obtenerResultset("SELECT eus_id id, eus_nombre nombre, eus_activo activo FROM seg.eus_estado_usuario");   
    }
    //Obtiene la lista de roles asociados
    public function obtenerRolesUsuario(){
      return $this->obtenerResultset("SELECT  r.usr_id usuarioid, r.tus_id tipousuarioid, tus_nombre nombretipousuario,
      t.vlc_id_tipo_entidad tipoentidadid, vlc_nombre nombretipoentidad, r.rou_entidadid entidadid,
      coalesce(eap_nombre, upu_nombre, mnc_nombre, '(No requerida)') nombreentidad,
      u.usr_primer_nombre || ' ' || u.usr_primer_apellido || ' ' || to_char(r.rou_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria 
      from seg.rou_rol_usuario r
      inner join seg.tus_tipo_usuario t on t.tus_id = r.tus_id
      inner join conf.vlc_valor_catalogo v on v.vlc_id = t.vlc_id_tipo_entidad
      left join seg.usr_usuario u on u.usr_id = r.usr_id_auditoria 
      left join conf.eap_eapb e on e.eap_id = r.rou_entidadid and v.vlc_codigo = 'EAPB'
      left join conf.upu_upgd_ui i on i.upu_id = r.rou_entidadid and v.vlc_codigo = 'UPGD'
      left join conf.mnc_municipio m on m.mnc_id = r.rou_entidadid and v.vlc_codigo = 'SECR'
      where r.usr_id = :id");
    }
    //Insertar roles de un usuario
    public function insertarRolUsuario(){
      return $this->actualizarData("INSERT INTO seg.rou_rol_usuario (usr_id, tus_id, rou_entidadid, usr_id_auditoria, rou_fecha_auditoria)
      values(:usuarioid, :tipousuarioid, :entidadrol, :usuario, current_timestamp)", null, true);
    }
    //Eliminar roles de un usuario
    public function eliminarRolUsuario(){
      return $this->actualizarData("DELETE FROM seg.rou_rol_usuario where usr_id = :usuarioid 
          and tus_id = :tipousuarioid and rou_entidadid = :entidadid");
    }
    //Inserta o actualiza el notificador
    public function establecerNotificador(int $id, bool $nuevo){
      $params = array();
      $params["id"] = $id;
      $params["pregrado"] = $this->parametros["pregrado"];
      $params["registro_medico"] = $this->parametros["registro_medico"];
      $params["autoriza_email"] = $this->parametros["autoriza_email"];
      $params["autoriza_sms"] = $this->parametros["autoriza_sms"];
      $params["especialidadid"] = $this->parametros["especialidadid"];
      if($nuevo) {//Insertar
        //throw new Exception(implode("|", $params));

        return $this->actualizarData("INSERT INTO oper.ntf_notificador(ntf_id, ntf_pregrado, ntf_registro_medico, ntf_autoriza_email, 
          ntf_autoriza_sms, ntf_fecha_auditoria, usr_id_auditoria, vlc_id_especialidad)
          VALUES (:id, :pregrado, :registro_medico, :autoriza_email, :autoriza_sms, current_timestamp, :usuario, :especialidadid)", 
          $params, true);
      } else { //Actualizar
        
        return $this->actualizarData("UPDATE oper.ntf_notificador SET ntf_pregrado = :pregrado, ntf_registro_medico = :registro_medico, 
          ntf_autoriza_email = :autoriza_email, ntf_autoriza_sms = :autoriza_sms, ntf_fecha_auditoria = current_timestamp, 
          usr_id_auditoria= :usuario, vlc_id_especialidad = :especialidadid WHERE ntf_id = :id", 
          $params, true);
      }
    }
    //Inserta o actualiza al responsable
    public function establecerResponsable(int $id, bool $nuevo){
      $params = array();
      $params["id"] = $id;
      $params["autoriza_email"] = $this->parametros["autoriza_email"];
      $params["autoriza_sms"] = $this->parametros["autoriza_sms"];
      if($nuevo) {//Insertar

        return $this->actualizarData("INSERT INTO oper.rps_responsable(rps_id, rps_autoriza_email, rps_autoriza_sms,  
            rps_fecha_auditoria, usr_id_auditoria)
            VALUES (:id, :autoriza_email, :autoriza_sms, current_timestamp, :usuario)", $params, true);
      } else { //Actualizar
        
        return $this->actualizarData("UPDATE oper.rps_responsable SET rps_autoriza_email = :autoriza_email, 
            rps_autoriza_sms = :autoriza_sms, rps_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario
            WHERE rps_id = :id", $params, true);
      }
    }
  //Obtener usuario por id
  public function obtenerNotificador(){
      return $this->obtenerResultset("SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
        u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
        u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
        u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento,
        a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(n.ntf_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
        ntf_pregrado pregrado, ntf_registro_medico registro_medico, ntf_autoriza_email autoriza_email, 
        ntf_autoriza_sms autoriza_sms, vlc_id_especialidad especialidadid
        from seg.usr_usuario u left join oper.ntf_notificador n on n.ntf_id = u.usr_id
        left join seg.usr_usuario a on a.usr_id = n.usr_id_auditoria 
        where u.usr_id = :id")[0];
  }
  //Obtener usuario por id
  public function obtenerResponsable(){
    return $this->obtenerResultset("SELECT distinct u.usr_id id, u.eus_id estado, u.tid_id tipoidentificacionid, u.usr_identificacion identificacion,
      u.usr_primer_nombre primer_nombre, u.usr_segundo_nombre segundo_nombre, u.usr_primer_apellido primer_apellido,
      u.usr_segundo_apellido segundo_apellido, u.usr_email email, u.usr_telefonos telefonos, u.usr_fecha_acceso fecha_acceso,
      u.usr_fecha_activacion fecha_activacion, u.usr_fecha_intento fecha_intento,
      a.usr_primer_nombre || ' ' || a.usr_primer_apellido || ' ' || to_char(n.rps_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria,
      rps_autoriza_email autoriza_email, rps_autoriza_sms autoriza_sms
      from seg.usr_usuario u left join oper.rps_responsable n on n.rps_id = u.usr_id
      left join seg.usr_usuario a on a.usr_id = n.usr_id_auditoria
      where u.usr_id = :id")[0];
}
}