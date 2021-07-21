<?php

namespace App\Http\Controllers\API\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Parametro;
use App\Models\Enum\ENUM_OPC;
use App\Models\Seguridad\Notificador;
use App\Models\Seguridad\Responsable;
use App\Models\Seguridad\Usuario;
use Exception;
use Illuminate\Http\Request;

//Controlador de eventos de usuario
class UsuarioController extends Controller
{
    //Lista de usuarios desde la consulta
    public function consultarUsuarios(Request  $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        return $usuario->consultarUsuarios();
    }
    //Autenticar al usuario desde la página de login
    public function autenticar(Request $request) {
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        return $usuario->autenticar();
    }
    //Autenticar al usuario por el token guardado en las cookies del cliente
    public function autenticarporToken(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->autenticarporToken();;
    }
    //Enviar correo 
    public function enviarcorreo(Request $request){
        //Inicializar la respuesta
        $rta = [];
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        $usuario->iniciarTransaccion();
        try {
            $data = $usuario->enviarCorreo();
            //Exitoso
            $rta = array("codigo" => $data["registro"]?1:2, "descripcion" => "Exitoso", "data" => $data);
            $usuario->serializarTransaccion();
        } catch(\Exception $ex) { //Fallido
            $usuario->abortarTransaccion();
            $rta = array("codigo" => 0, "descripcion" => $ex->getMessage());
        }
        return $rta;
    }
    //Obtiene las opciones de menú del usuario
    public function obtenerMenuUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        return $usuario->obtenerMenuUsuario();
    }
    //Eliminar usuario
    public function eliminarUsuario(Request $request){
        $usuario = new Usuario($request, 25); 
        $rta["codigo"] = $usuario->eliminarUsuario();
        $rta["descripcion"] = "Falla en la eliminación";
        return $rta;
    }
    //Cambiar Clave
    public function cambiarClave(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        $prmGeneral = new Parametro($request, ENUM_OPC::OPCION_GENERAL);

        $intento = $usuario->autenticar(true);
        $rta["codigo"] = 0;
        //No se pudo autenticar
        if(count($intento) == 0) {
            $rta["descripcion"] = "Clave actual no es válida. Esta acción bloqueará a su usuario si se supera el número de intentos permitidos.";
            return $rta;
        }
        //Verificar complejidad clave
        $patron = $prmGeneral->obtenerParametroporCodigo("CMCLAV")[0]->valor;
        //throw new Exception($patron);
        $mensaje = $prmGeneral->obtenerParametroporCodigo("EXPCLA")[0]->valor;
        $clave = $usuario->parametros["clavenueva"];
        if (preg_match($patron, $clave)==0) {
            $rta["descripcion"] = "Calidad de clave insuficiente para ser aceptada. Por favor recuerde : " . $mensaje;
            return $rta;
        }

        $resultado = $usuario->cambiarClave();
        if($resultado==true) {
            $rta["codigo"] = 1;
            $rta["descripcion"] = "Exitoso";
        } else {
            $rta["codigo"] = 0;
            $rta["descripcion"] = "El cambio de clave no fue posible. ".
                "Recuerde que su clave no puede ser igual a alguna de las anteriores.";
        }
        return $rta;
    }
    //Obtener usuario por id
    public function obtenerUsuarioporID(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->obtenerUsuarioporID();   
    }
    //Obtener usuario por id
    public function obtenerNotificador(Request $request){
        $notificador = new Notificador($request, ENUM_OPC::OPCION_GENERAL); 
        return json_encode($notificador->obtenerNotificador());    
    }
    //Obtener usuario por id
    public function obtenerResponsable(Request $request){
        $responsable = new Responsable($request, ENUM_OPC::OPCION_GENERAL); 
        return json_encode($responsable->obtenerResponsable());    
    }
    //Establece el usuario y retorna el número
    public function establecerUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        $usuario->iniciarTransaccion();
        $rta = $usuario->establecerUsuario();   
        $usuario->serializarTransaccion();
        return $rta;
    }
    //Obtiene la lista de roles posibles de la base de datos
    public function obtenerTiposUsuario(Request $request) {
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->obtenerTiposUsuario();   
    }
    //Obtiene la lista de estados del usuario
    public function obtenerEstadosUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->obtenerEstadosUsuario();
    }
    //Obtener lista de roles asociados al usuario
    public function obtenerRolesUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->obtenerRolesUsuario();   
    }
    //Insertar nuevo rol para el usuario
    public function insertarRolUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->insertarRolUsuario();   
    }
    //Eliminar un rol del usuario
    public function eliminarRolUsuario(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL); 
        return $usuario->eliminarRolUsuario();   
    }
    //Parametros para establecer el usuario notificador/responsable
    private function paramsUsuario($usuario) {
        return array("tipoidentificacionid" => $usuario->parametros["tipoidentificacionid"], 
        "identificacion" => $usuario->parametros["identificacion"], 
        "primer_nombre" => $usuario->parametros["primer_nombre"], 
        "segundo_nombre" => $usuario->parametros["segundo_nombre"], 
        "primer_apellido" => $usuario->parametros["primer_apellido"], 
        "segundo_apellido" => $usuario->parametros["segundo_apellido"], 
        "email" => $usuario->parametros["email"], 
        "telefonos" => $usuario->parametros["telefonos"],
        "estado" => 1,
        "id" => $usuario->parametros["id"]);
    }
    //Establece la información de un usuario notificador
    public function establecerNotificador(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        $notificador = new Notificador($request, ENUM_OPC::OPCION_GENERAL);
        try {
            $data = null;
            //Obtener los parámetros de usuario
            $params = $this->paramsUsuario($usuario);
            //Verifica si es una operación de registro
            $nuevo = ($params["id"] <= 0);
            $esregistro = ($params["id"]==null);
            $usuario->iniciarTransaccion();
            //Establece al usuario     
            $id = $usuario->establecerUsuario($params);
            //Se inserta rol de médico solo si es registro
            if($esregistro) $usuario->insertarRolUsuario(array("usuarioid"=> $id,"tipousuarioid"=> 1, "entidadrol"=> 0));
            //Insertar notificador
            $notificador->establecerNotificador($id, $nuevo);
            //Se verifica si está en registro
            if($esregistro) {
                $params = array("emailidentificacion"=> $params["email"], 
                                "tipousuario"=> 1,
                                "metodoautenticacion"=> 3);
                $data = $usuario->enviarCorreo($params);
            }

            //TODO: Verificar funcionamiento
            $usuario->serializarTransaccion();
            return array("codigo" => 1, "descripcion" => "Exitoso", "data"=> $data);
        } catch(Exception $e) {
            return array("codigo" => 0, "descripcion" => $e->getMessage());
        }
    }
    //Establece la información de un usuario notificador
    public function establecerResponsable(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_GENERAL);
        $notificador = new Responsable($request, ENUM_OPC::OPCION_GENERAL);
        try {
            $data = null;
            //Obtener los parámetros de usuario
            $params = $this->paramsUsuario($usuario);
            //Verifica si es una operación de registro
            $nuevo = ($params["id"] <= 0);
            $esregistro = ($params["id"]==null);
            $usuario->iniciarTransaccion();
            //Establece al usuario     
            $id = $usuario->establecerUsuario($params);
            //Se inserta rol de médico
            if($esregistro) $usuario->insertarRolUsuario(array("usuarioid"=> $id,"tipousuarioid"=> 2, "entidadrol"=> 0));
            //Se establece el responsable
            $notificador->establecerResponsable($id, $nuevo);
            //Se verifica si está en registro
            if($esregistro) {
                $params = array("emailidentificacion"=> $params["email"], 
                                "tipousuario"=> 1,
                                "metodoautenticacion"=> 2);
                $data = $usuario->enviarCorreo($params);
            }
            //TODO: Verificar funcionamiento
            $usuario->serializarTransaccion();
            return array("codigo" => 1, "descripcion" => "Exitoso", "data"=> $data);
        } catch(Exception $e) {
            return array("codigo" => 0, "descripcion" => $e->getMessage());
        }
    }
    //Autenticar al usuario por sesión temporal
    public function autenticarPorSesionTemporal(Request $request){
        $usuario = new Usuario($request, ENUM_OPC::OPCION_SIN_SESION); 
        return $usuario->autenticarPorSesionTemporal();
    }
    //Consultar participantes
    public function consultarParticipantes(Request  $request){
        $usuario = new Usuario($request, ENUM_OPC::PARTICIPANTES);
        return $usuario->consultarParticipantes();
    }
}
