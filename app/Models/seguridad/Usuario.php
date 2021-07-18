<?php

namespace App\Models\Seguridad;

use App\Mail\msgUsuario;
use App\Models\Configuracion\Parametro;
use App\Models\Core;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        $this->usuario = $rta[0];
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
    function enviarCorreo(array $params = null) {
      if($params == null) $params = $this->parametros;
      $dato = $params["emailidentificacion"];
      $tipousuario = $params["tipousuario"];
      $sqlparams = array("emailidentificacion" => $dato,
                    "tipo" => $params["tipousuario"],
                    "ip" => $this->variablesServidor["ip"]);
      //throw new Exception(implode("|", $sqlparams)."metodo " . $params["metodoautenticacion"] );
      $data = null;
      if($params["metodoautenticacion"] == 1 && ($tipousuario == 1 || $tipousuario == 2)) {//usuario nuevo solo puede ser médico o responsable
        $rta = DB::select('SELECT * FROM seg.fnsst_iniciar(:emailidentificacion, :tipo,  :ip)',$sqlparams);
        if($rta[0]->sesion == "ESUSUARIO") 
          throw new Exception("El correo pertenece a un usuario registrado. Por favor, contactese con Delfos o seleccione la opción adecuada si se le olvidó la clave");
        if($rta[0]->sesion == "")
          throw new Exception("Se ha intentado generar un nuevo correo desde otra IP (Identificación de red). Por seguridad, debe esperar al menos una hora para volver a intentarlo");
        if($rta[0]->sesion == "5MINUTOS")
          throw new Exception("Por favor, si no ha recibido el correo revise en su bandeja de spam. Si no lo encuentra, por seguridad, se requiere que espere 5 minutos para volver a intentarlo");
        $data =  array("minutos"=> $rta[0]->minutos, "registro" => true);
        $mensaje["servidor"] = $rta[0]->servidor;
        $mensaje["token"] = $rta[0]->sesion;
        Mail::to($dato)->send(new msgUsuario($mensaje, "Registro Delfos"));
      } else if ($params["metodoautenticacion"] == 3) { //Usuario olvidó clave enviar clave provisional
        $claves = $this->crearClave();
        $sqlparams["clave"] = $claves["claveencriptada"];

        $rta = DB::select('SELECT  * FROM seg.fnusr_generarclavealeatoria(:emailidentificacion, :tipo,  :ip, :clave)',$sqlparams);
        if(count($rta)==0)
          throw new Exception("El usuario no fue encontrado");
        if(!$rta[0]->generaclave)
          throw new Exception("Al usuario no se le concedió clave porque se le acaba de enviar. Por seguridad, se requiere que espere 5 minutos para volver a intentarlo");
        $data = array("minutos"=> $rta[0]->minutos, "clave"=> $claves["nuevaclave"], "registro" => false);
        $mensaje["servidor"] = $rta[0]->servidor;
        $mensaje["nombre"] = $rta[0]->nombre;
        $mensaje["clave"] = $claves["nuevaclave"];
        Mail::to($dato)->send(new msgUsuario($mensaje, "Nueva clave Delfos", 'mails.correoClave'));
      } else {
        throw new Exception("Solicitud no válida para la aplicación");
      }
      return $data; //$rta[0]->fnsst_iniciar;//"fnsst_iniciar"];
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
      $observacion = "Consultar Usuarios";
      $this->insertarAuditoria(Core::$usuarioID,3, $observacion, true, "C", ""); 
      return $rta;
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
        $lista = $this->obtenerResultset("DELETE from seg.usr_usuario where usr_id = :id RETURNING usr_identificacion", $this->parametros);
        $rta = 1;
        $observacion = "Usuario ID: " . $this->parametros["id"] . ". Identificación: " . $lista[0]->usr_identificacion;
        $this->insertarAuditoria(Core::$usuarioID, 3, $observacion, true, "E", "");     
      } catch(\Exception $ex){ //Si el usuario ya tiene referencias se inactiva
        $rta = 2;
        $this->actualizarData("UPDATE seg.usr_usuario set eus_id = 3 where usr_id = :id", $this->parametros);
        $observacion = "Usuario ID : " . $this->parametros["id"] . ". Usuario inactivado, no borrado. Razón: Elementos asociados";
        $this->insertarAuditoria(Core::$usuarioID, 3, $observacion, true, "M", "");  
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
    //Crear nueva clave 
    public function crearClave() {
      $nuevaclave = DB::select("SELECT seg.random_string(10)")[0]->random_string;
      $claveencriptada = $this->encriptarClave($nuevaclave);
      return array("nuevaclave"=>$nuevaclave,"claveencriptada"=>$claveencriptada);
    }
    //Cambiar Clave. Retorna 0 si es exitosa o el número de claves no repetidas
    public function cambiarClave(){
      $params = $this->parametros;
      $params["claveanterior"] = $this->encriptarClave($params["claveanterior"]);
      $params["clavenueva"] = $this->encriptarClave($params["clavenueva"]);
      //throw new Exception(implode("|", $params));
      $rta = $this->obtenerResultset("SELECT * FROM seg.fnusr_actualizarclave(:id,:claveanterior,:clavenueva, :usuario)", 
              $params, true, ["confirmarclave","emailidentificacion"]);
      if($rta[0]->fnusr_actualizarclave) {
        $observacion = "";
        $this->insertarAuditoria(Core::$usuarioID, 2, "Cambio de Clave", true, "M", $observacion); //Existe el usuario
      }
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
      if($params == null) $params = $this->parametros;
      if($this->parametros["id"]<= 0) {//Insertar
        $rta = null;
        if($params["estado"] == null) $params["estado"] = 1;
        $rta = $this->obtenerResultset("INSERT INTO seg.usr_usuario(usr_id, eus_id, tid_id, usr_identificacion, 
            usr_primer_nombre, usr_segundo_nombre, usr_primer_apellido, usr_segundo_apellido, usr_email, usr_telefonos, 
            usr_fecha_auditoria, usr_id_auditoria, usr_intentos, usr_fecha_creacion)
          VALUES (nextval('seg.sequsr'), :estado, :tipoidentificacionid, :identificacion, 
          :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :email, :telefonos,
            current_timestamp, case when :usuario <=0 then null else :usuario end, 0, current_timestamp) RETURNING usr_id", 
            $params, true, ["auditoria","id", "clave"])[0]->usr_id;
        if(Core::$usuarioID<=0) Core::$usuarioID = $rta;
        $observacion = "Usuario ID: " . $rta . ". Identificacion: " . $params["identificacion"];
        $this->insertarAuditoria(Core::$usuarioID,3, $observacion, true, "I", ""); //Existe el usuario
                
      } else { //Actualizar
        //throw new Exception(implode("|", array_keys($params)));
         $this->actualizarData("UPDATE seg.usr_usuario SET eus_id = :estado, tid_id = :tipoidentificacionid,
            usr_identificacion = :identificacion, usr_primer_nombre = :primer_nombre, usr_segundo_nombre = :segundo_nombre, 
            usr_primer_apellido = :primer_apellido, usr_segundo_apellido = :segundo_apellido, usr_email = :email, 
            usr_telefonos = :telefonos, usr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario, 
            usr_intentos = 0, usr_fecha_intento = null
            WHERE usr_id =  :id", $params, true, ["clave","auditoria"]);
         $rta = $this->parametros["id"];
         $observacion = "Usuario ID: " . $this->parametros["id"] . ". Identificacion: " . $params["identificacion"];
         $this->insertarAuditoria(Core::$usuarioID,3, $observacion, true, "M", ""); //Existe el usuario
       }
      return $rta;
    }
    //Obtiene la lista de roles posibles de la base de datos
    public function obtenerTiposUsuario() {
      $params = $this->parametros;
      $existe = array_key_exists("externo", $params);
      if(!($existe)) $params["externo"]=null;
      return $this->obtenerResultset("SELECT tus_id id, tus_nombre nombre, vlc_nombre nombretipoentidad, 
      t.vlc_id_tipo_entidad tipoentidadid, vlc_codigo codigotipoentidad
      FROM seg.tus_tipo_usuario t inner join conf.vlc_valor_catalogo v on v.vlc_id = t.vlc_id_tipo_entidad
      WHERE tus_externo = coalesce(:externo, tus_externo)", $params);   
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
    public function insertarRolUsuario(array $params = null){
      if($params==null) $params = $this->parametros;
      $rta = $this->actualizarData("INSERT INTO seg.rou_rol_usuario (usr_id, tus_id, rou_entidadid, usr_id_auditoria, rou_fecha_auditoria)
      values(:usuarioid, :tipousuarioid, :entidadrol, :usuario, current_timestamp)", $params, true);
      $observacion = "Usuario ID: " . $params["usuarioid"] . ". Rol ID: " . $params["entidadrol"];
      $this->insertarAuditoria(Core::$usuarioID,5, $observacion, true, "I", ""); //Existe el usuario
      return $rta;
    }
    //Eliminar roles de un usuario
    public function eliminarRolUsuario(){
      $rta = $this->actualizarData("DELETE FROM seg.rou_rol_usuario where usr_id = :usuarioid 
          and tus_id = :tipousuarioid and rou_entidadid = :entidadid");
      $observacion = "Usuario ID: " . $this->parametros["usuarioid"] . ". Rol ID: " . $this->parametros["entidadid"];
      $this->insertarAuditoria(Core::$usuarioID,5, $observacion, true, "E", ""); //Existe el usuario
      return $rta;
    }
    //Funciónes de sesión temporal ---------------------------------------------------------------------
    public function autenticarPorSesionTemporal(){
      $this->validarSesion();
      return (- CORE::$usuarioID);
    }
    //Fin de funciónes de sesión temporal --------------------------------------------------------------  
}