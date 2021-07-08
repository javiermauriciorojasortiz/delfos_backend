<?php

namespace App\Http\Controllers\API\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Parametro;
use App\Models\Seguridad\Usuario;
use Exception;
use Illuminate\Http\Request;

//Controlador de eventos de usuario
class UsuarioController extends Controller
{
    //Lista de usuarios desde la consulta
    public function consultarUsuarios(Request  $request){
        $usuario = new Usuario($request, 0);
        return $usuario->consultarUsuarios();
    }
    //Autenticar al usuario desde la página de login
    public function autenticar(Request $request) {
        $usuario = new Usuario($request, 0);
        return $usuario->autenticar();
    }
    //Autenticar al usuario por el token guardado en las cookies del cliente
    public function autenticarporToken(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->autenticarporToken();;
    }
    //Enviar correo 
    public function enviarcorreo(Request $request){
        //Inicializar la respuesta
        $rta = [];
        $usuario = new Usuario($request, 0);
        $usuario->iniciarTransaccion();
        try {
            $data = $usuario->enviarCorreo();
            //Exitoso
            $rta = array("codigo" => 1, "descripcion" => "Exitoso", "data" => $data);
            $usuario->serializarTransaccion();
        } catch(\Exception $ex) { //Fallido
            $usuario->abortarTransaccion();
            $rta = array("codigo" => 0, "descripcion" => $ex->getMessage());
        }
        return $rta;
    }
    //Obtiene las opciones de menú del usuario
    public function obtenerMenuUsuario(Request $request){
        $usuario = new Usuario($request, 0);
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
        $usuario = new Usuario($request, 0); 
        $prmGeneral = new Parametro($request, 0);

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
        $usuario = new Usuario($request, 0); 
        return $usuario->obtenerUsuarioporID();   
    }
    //Obtener usuario por id
    public function obtenerNotificador(Request $request){
        $usuario = new Usuario($request, 0); 
        return json_encode($usuario->obtenerNotificador());    
    }
    //Obtener usuario por id
    public function obtenerResponsable(Request $request){
        $usuario = new Usuario($request, 0); 
        return json_encode($usuario->obtenerResponsable());    
    }
    //Establece el usuario y retorna el número
    public function establecerUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->establecerUsuario();   
    }
    //Obtiene la lista de roles posibles de la base de datos
    public function obtenerTiposUsuario(Request $request) {
        $usuario = new Usuario($request, 0); 
        return $usuario->obtenerTiposUsuario();   
    }
    //Obtiene la lista de estados del usuario
    public function obtenerEstadosUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->obtenerEstadosUsuario();
    }
    //Obtener lista de roles asociados al usuario
    public function obtenerRolesUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->obtenerRolesUsuario();   
    }
    //Insertar nuevo rol para el usuario
    public function insertarRolUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->insertarRolUsuario();   
    }
    //Eliminar un rol del usuario
    public function eliminarRolUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
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
        $usuario = new Usuario($request, 0);
        $nuevo = ($usuario->parametros["id"] == 0);
        //Obtener los parámetros de usuario
        $params = $this->paramsUsuario($usuario);

        $usuario->iniciarTransaccion();        
        $id = $usuario->establecerUsuario($params);
        $id = $usuario->establecerNotificador($id, $nuevo);
        $usuario->serializarTransaccion();
        return $id;
    }
    //Establece la información de un usuario notificador
    public function establecerResponsable(Request $request){
        $usuario = new Usuario($request, 0); 
        $nuevo = ($usuario->parametros["id"] == 0);
        //Obtener los parámetros de usuario
        $params = $this->paramsUsuario($usuario);
        $usuario->iniciarTransaccion();
        $id = $usuario->establecerUsuario($params);
        $usuario->establecerResponsable($id, $nuevo);   
        $usuario->serializarTransaccion();
        return $id;
    }
}
