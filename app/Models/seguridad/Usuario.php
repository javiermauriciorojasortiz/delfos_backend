<?php

namespace App\Models\Seguridad;

use App\Mail\msgUsuario;
use App\Models\Configuracion\Parametro;
use App\Models\Core;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_SEG;
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
      $rta = DB::select(QUERY_SEG::_USR_AUTENTICAR, $params);
      
      if(count($rta) > 0) {
        $observacion = "Estado " . $rta[0]->estadonombre;
        $this->insertarAuditoria($rta[0]->id, ENUM_AUD::SOLICITUD_ACCESO, "Autenticación por clave", true, "G", $observacion); //Existe el usuario
        $this->usuario = $rta[0];
      }  
      return $rta;
    }
    //Obtiene el usuario si el login es correcto
    function autenticarporToken(){
      $params = $this->parametros;
      $params["ip"] = $this->variablesServidor["ip"];
      $rta = DB::select(QUERY_SEG::_USR_AUTENTICARXTOKEN, $params);
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
        $rta = DB::select(QUERY_SEG::_SST_INICIAR,$sqlparams);
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

        $rta = DB::select(QUERY_SEG::_USR_GENERARCLAVEALEATORIA,$sqlparams);
        if(count($rta)==0)
          throw new Exception("El usuario no fue encontrado");
        if(!$rta[0]->generaclave)
          throw new Exception("Al usuario no se le concedió clave porque se le acaba de enviar. Por seguridad, se requiere que espere 5 minutos para volver a intentarlo");
        $data = array("minutos"=> $rta[0]->minutos, "clave"=> $claves["nuevaclave"], "registro" => false);
        $mensaje["servidor"] = $rta[0]->servidor;
        $mensaje["nombre"] = $rta[0]->nombre;
        $mensaje["clave"] = $claves["nuevaclave"];
        $observacion = "Enviar correo clave aleatoria";
        Core::$usuarioID = $rta[0]->usuarioid;
        Mail::to($dato)->send(new msgUsuario($mensaje, "Nueva clave Delfos", 'mails.correoClave'));
        $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::USUARIO, $observacion, true, "C", ""); 
      } else {
        throw new Exception("Solicitud no válida para la aplicación");
      }
      return $data; //$rta[0]->fnsst_iniciar;//"fnsst_iniciar"];
    }
    //Obtener lista de usuarios
    function consultarUsuarios() {
      $rta = $this->obtenerResultset(QUERY_SEG::_USR_CONSULTAR);
      $observacion = "Consultar Usuarios";
      $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::USUARIO, $observacion, true, "C", ""); 
      return $rta;
    }
    //Obtener menú del usuario autenticado
    function obtenerMenuUsuario() {
      $rta = $this->obtenerResultset(QUERY_SEG::_USR_OBTENERMENU, null, true);
      return $rta;
    }
    //Eliminar usuario
    public function eliminarUsuario(){
      $rta = 0;
      try {
        $lista = $this->obtenerResultset(QUERY_SEG::_USR_ELIMINAR, $this->parametros);
        $rta = 1;
        $observacion = "Usuario ID: " . $this->parametros["id"] . ". Identificación: " . $lista[0]->usr_identificacion;
        $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::USUARIO, $observacion, true, "E", "");     
      } catch(\Exception $ex){ //Si el usuario ya tiene referencias se inactiva
        $rta = 2;
        $this->actualizarData(QUERY_SEG::_USR_INACTIVAR, $this->parametros);
        $observacion = "Usuario ID : " . $this->parametros["id"] . ". Usuario inactivado, no borrado. Razón: Elementos asociados";
        $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::USUARIO, $observacion, true, "M", "");  
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
      $nuevaclave = DB::select(QUERY_SEG::_USR_GENERARNUEVACLAVE)[0]->random_string;
      $claveencriptada = $this->encriptarClave($nuevaclave);
      return array("nuevaclave"=>$nuevaclave,"claveencriptada"=>$claveencriptada);
    }
    //Cambiar Clave. Retorna 0 si es exitosa o el número de claves no repetidas
    public function cambiarClave(){
      $params = $this->parametros;
      $params["claveanterior"] = $this->encriptarClave($params["claveanterior"]);
      $params["clavenueva"] = $this->encriptarClave($params["clavenueva"]);
      //throw new Exception(implode("|", $params));
      $rta = $this->obtenerResultset(QUERY_SEG::_USR_CAMBIARCLAVE, $params, true, ["confirmarclave","emailidentificacion"]);
      if($rta[0]->fnusr_actualizarclave) {
        $observacion = "";
        $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::CAMBIAR_CLAVE, "Cambio de Clave", true, "M", $observacion); //Existe el usuario
      }
      return $rta[0]->fnusr_actualizarclave;
    }
    //Obtener usuario por id
    public function obtenerUsuarioporID(){
        $rta = $this->obtenerResultset(QUERY_SEG::_USR_OBTENERPORID);
    }
    //Establece el usuario y retorna el número
    public function establecerUsuario(array $params = null){
      if($params == null) $params = $this->parametros;
      if($this->parametros["id"]<= 0) {//Insertar
        $rta = null;
        if($params["estado"] == null) $params["estado"] = 1;
        $rta = $this->obtenerResultset(QUERY_SEG::_USR_INSERTAR, $params, true, ["auditoria","id", "clave"])[0]->usr_id;
        if(Core::$usuarioID<=0) Core::$usuarioID = $rta;
        $observacion = "Usuario ID: " . $rta . ". Identificacion: " . $params["identificacion"];
        $this->insertarAuditoria(Core::$usuarioID,ENUM_AUD::USUARIO, $observacion, true, "I", ""); //Existe el usuario
        
      } else { //Actualizar
        //throw new Exception(implode("|", array_keys($params)));
         $this->actualizarData(QUERY_SEG::_USR_ACTUALIZAR, $params, true, ["clave","auditoria"]);
         $rta = $this->parametros["id"];
         $observacion = "Usuario ID: " . $this->parametros["id"] . ". Identificacion: " . $params["identificacion"];
         $this->insertarAuditoria(Core::$usuarioID,ENUM_AUD::USUARIO, $observacion, true, "M", ""); //Existe el usuario
       }
      return $rta;
    }
    //Obtiene la lista de roles posibles de la base de datos
    public function obtenerTiposUsuario() {
      $params = $this->parametros;
      return $this->obtenerResultset(QUERY_SEG::_TUS_CONSULTAR, $params);   
    }
    //Obtiene la lista de estados del usuario
    public function obtenerEstadosUsuario(){
      return $this->obtenerResultset(QUERY_SEG::_EUS_CONSULTAR);   
    }
    //Obtiene la lista de roles asociados
    public function obtenerRolesUsuario(){
      return $this->obtenerResultset(QUERY_SEG::_USR_OBTENERROL);
    }
    //Insertar roles de un usuario
    public function insertarRolUsuario(array $params = null){
      if($params==null) $params = $this->parametros;
      $rta = $this->actualizarData(QUERY_SEG::_USR_INSERTARROL, $params, true);
      $observacion = "Usuario ID: " . $params["usuarioid"] . ". Rol ID: " . $params["entidadrol"];
      $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::ROL, $observacion, true, "I", ""); //Existe el usuario
      return $rta;
    }
    //Eliminar roles de un usuario
    public function eliminarRolUsuario(){
      $rta = $this->actualizarData(QUERY_SEG::_USR_ELIMINARROL);
      $observacion = "Usuario ID: " . $this->parametros["usuarioid"] . ". Rol ID: " . $this->parametros["entidadid"];
      $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::ROL, $observacion, true, "E", ""); //Existe el usuario
      return $rta;
    }
    //Funciónes de sesión temporal ---------------------------------------------------------------------
    public function autenticarPorSesionTemporal(){
      $this->validarSesion();
      return (- CORE::$usuarioID);
    }
    //Fin de funciónes de sesión temporal --------------------------------------------------------------  

    //Consultar participantes
    public function consultarParticipantes(){
      $rta = $this->obtenerResultset(QUERY_SEG::_USR_CONSULTARPARTICIPANTE);
      $observacion = "Consultar Participantes";
      $this->insertarAuditoria(Core::$usuarioID, ENUM_AUD::PARTICIPANTE, $observacion, true, "C", ""); 
      return $rta;
    }
}