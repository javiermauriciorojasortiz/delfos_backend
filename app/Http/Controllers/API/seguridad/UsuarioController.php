<?php

namespace App\Http\Controllers\API\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\Usuario;
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
        $resultado = $usuario->cambiarClave();
        if($resultado==0) {
            $rta = array("codigo" => 1, "descripcion" => "Exitoso");
        } else {
            $rta = array("codigo" => 0, "descripcion" => "El cambio de clave no fue posible. ".
                "Recuerde que su clave no puede ser igual a alguna de las " . $resultado . " anteriores.");
        }
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
}
