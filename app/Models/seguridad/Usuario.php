<?php

namespace App\Models\Seguridad;

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
    function autenticar(){
      $params = $this->parametros;
      $params["ip"] = $this->variablesServidor["ip"];
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
      $this->usuario = $rta;
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
      $rta = $this->obtenerResultset("SELECT distinct usr_id id, u.eus_id estado, u.tid_id tipoidentificacion, u.usr_identificacion identificacion,
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
            limit 1000", $this->parametros);
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
        $rta = $this->actualizarData("DELETE from seg.usr_usuario where usr_id = :id", $this->parametros);
      } catch(\Exception $ex){ //Si el usuario ya tiene referencias se inactiva
        $rta = $this->actualizarData("UPDATE seg.usr_usuario set eus_id = 3 where usr_id = :id", $this->parametros);
      }
      return $rta;
    }
    //Cambiar Clave. Retorna 0 si es exitosa o el número de claves no repetidas
    public function cambiarClave(){
        $rta = $this->obtenerResultset("SELECT * FROM seg.fnusr_actualizarclave(:id,:claveanterior,:clavenueva)", 
          $this->parametros);
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
    public function establecerUsuario(){

      if($this->parametros["id"]== 0) {//Insertar

        return $this->actualizarData("INSERT INTO seg.usr_usuario(usr_id, eus_id, tid_id, usr_identificacion, usr_clave, 
            usr_primer_nombre, usr_segundo_nombre, usr_primer_apellido, usr_segundo_apellido, usr_email, usr_telefonos, 
            usr_fecha_auditoria, usr_id_auditoria, usr_intentos, usr_fecha_creacion)
          VALUES (nextval('seg.sequsr'), 0, :tipoidentificacion, :identificacion, :clave, 
          :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :email: string, :telefonos,
            current_timestamp, :usuario, 0, current_timestamp)", null, true, ["estado","auditoria","id"]);
      } else { //Actualizar
        
        return $this->actualizarData("UPDATE seg.usr_usuario SET eus_id = :estado, tid_id = :tipoidentificacion,
            usr_identificacion = :identificacion, usr_primer_nombre = :primer_nombre, usr_segundo_nombre = :segundo_nombre, 
            usr_primer_apellido = :primer_apellido, usr_segundo_apellido = :segundo_apellido, usr_email = :email, 
            usr_telefonos = :telefonos, usr_fecha_auditoria = current_timestamp, usr_id_auditoria = :usuario, 
            usr_intentos = 0, usr_fecha_intento = null
            WHERE usr_id =  :id", null, true, ["clave","auditoria"]);
      }
    }
  //Obtiene la lista de roles posibles de la base de datos
  public function obtenerTiposUsuario() {
    return $this->obtenerResultset("SELECT tus_id id, tus_nombre nombre, cat_tipo_entidad tipoentidad FROM seg.tus_tipo_usuario");   
  }
  //Obtiene la lista de estados del usuario
  public function obtenerEstadosUsuario(){
    return $this->obtenerResultset("SELECT eus_id id, eus_nombre nombre, eus_activo activo FROM seg.eus_estado_usuario");   
  }
}