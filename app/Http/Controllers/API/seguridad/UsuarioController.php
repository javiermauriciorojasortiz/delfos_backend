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
        return $usuario->eliminarUsuario();
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
    public function obtenerRolesUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->obtenerRolesUsuario();   
    }
    public function insertarRolUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->insertarRolUsuario();   
    }
    public function eliminarRolUsuario(Request $request){
        $usuario = new Usuario($request, 0); 
        return $usuario->eliminarRolUsuario();   
    }
}
