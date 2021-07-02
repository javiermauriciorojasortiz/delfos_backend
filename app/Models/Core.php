<?php

namespace App\Models;

use App\Models\Seguridad\Auditoria;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase de gestión del usuario
class Core {
    //Información del header requerida por la aplicación
    public $variablesServidor = null;
    //Lista de parámetros preprocesados enviados desde el cliente
    public $parametros = null;
    //Verifica si ya se validó la sesión para hacer operaciones con la base de datos
    public static $sesionValidada = false;
    //Identificador de usuario de sesión
    public static $usuarioID;
    //Opción desde la que se realiza la operación
    private $opcion = 1000000;
    //Request enviado
    private $request;
    //Obtiene la información de usuario de la base de datos
    function __construct(Request $request, int $opcion) {
        //Obtener encabezados
        $this->variablesServidor = array ("Authorization" => $request->header("Authorization"),
                                          "ip" => $request->ip());
        //Obtener parámetros enviados
        $this->parametros = $this->obtenerParametros($request);
        $this->opcion = $opcion;
        $this->request = $request;
    }
    //Obtener el arreglo de valores de los parámetros enviados
    private function obtenerParametros(Request $request) : array {

      $params = $request->input();
      //throw new Exception(implode("|",array_keys($params)) . '------' . implode("|",$params));

      $valores = [];
      foreach(array_keys($params) as &$key) {
          $valor = $params[$key];
          //throw new Exception("el puto valor " . $valor);
          if(str_starts_with($key, "fecha"))
          {
               if($params[$key]== "") {
                  $valor = null;
              } else {
                  $fecha = (new DateTime());
                  $fecha->setDate($valor['year'], $valor['month'], $valor['day']);
                  $valor = $fecha;
              }
          } else if ($valor === "") {
              throw new Exception("nulifico el valor");
              $valor = null;
          }
          //throw new Exception(strtolower($key) . ":" . $valor);
          $valores[strtolower($key)] = $valor;// $valor;
      }
      return $valores;  
    }
    //Validar que la sesión se encuentre activa para ejecutar operaciones BD
    public function validarSesion() : void{
        $params = array("sesion" => $this->variablesServidor["Authorization"], 
                        "ip" => $this->variablesServidor["ip"], 
                        "opcion" => $this->opcion);
        $rta = DB::select("select seg.fnusr_validarsesion(:sesion, :ip, :opcion)", $params);
        Core::$usuarioID = $rta[0]->fnusr_validarsesion;
        if(Core::$usuarioID == 0) 
            throw new Exception("Sesión no activa. Por favor autentíquese nuevamente");
    }
    //Insertar auditoria
    public function insertarAuditoria(int $usuarioid, int $tipoAuditoria, string $descripcion, bool $exitoso, string $operacion, string $observacion = null) : void {
        $auditoria = new Auditoria($this->request, 0);
        Core::$usuarioID = $usuarioid;
        $auditoria->insertar($tipoAuditoria, $descripcion, $exitoso, $operacion, $observacion);
    }
    //-----------------------------------------------------------------------------
    //Funciones de base de datos: La aplicación solo ejecutará operaciones contra la BD por acá
    //-----------------------------------------------------------------------------
    //Ejecuta consultas preparadas en la aplicación y obtiene resultados en arreglo
    public function obtenerResultset(string $consulta, array $params = null, bool $addUsuario = false, array $excluir = null) {
        //Siempre se verifica que haya sesión activa para la petición
        if(Core::$sesionValidada == false) $this->validarSesion();
        Core::$sesionValidada = true;
        if($params == null) $params = $this->parametros;
        if($addUsuario == true) $params["usuario"] = Core::$usuarioID;
        if($excluir != null) {
           foreach($excluir as $campo) {
             unset($params[$campo]);
           }
        }

        return DB::select($consulta, $params);
    }
    //Ejecutar consulta sin retorno
    public function actualizarData(string $consulta, array $params = null, bool $addUsuario = false, array $excluir = null) : int {
         //Siempre se verifica que haya sesión activa para la petición
         if(Core::$sesionValidada == false) $this->validarSesion();
         Core::$sesionValidada = true;
         if($params === null) $params = $this->parametros;
         if($addUsuario === true) $params["usuario"] = Core::$usuarioID;
         if($excluir != null) {
            foreach($excluir as $campo) {
              unset($params[$campo]);
            }
         }
         return DB::update($consulta, $params);    
    }
    //Inicia transacciones
    public function iniciarTransaccion(){
        DB::beginTransaction();
    }
    //Reversa transacciones
    public function abortarTransaccion(){
        DB::rollBack();
    }
    //Salvar trabajo en la transacción
    public function serializarTransaccion(){
        DB::commit();
    }
}