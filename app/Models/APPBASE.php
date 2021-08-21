<?php

namespace App\Models;

use App\Models\Enum\ENUM_OPC;
use App\Models\Query\QUERY_SEG;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase de gestión del usuario
class APPBASE {
  //Identificador de usuario
  public $usuarioID = 0;
  public $opcion = "";
  public $request = null;
  public $parametros = array();

  //Obtiene la información de usuario de la base de datos
  function __construct(Request $request, $opcion) {
    //Obtener encabezados
    $this->variablesServidor = array ("Authorization" => $request->header("Authorization"), "ip" => $request->ip());
    //Obtener parámetros enviados
    $this->parametros = $this->obtenerParametros($request);
    if(is_array($opcion)) { $this->opcion = implode("," , $opcion);} else {$this->opcion = $opcion;}

    $this->request = $request;
    //Validar por opción con sesión de usuario o temporal solamente
    if($opcion != ENUM_OPC::OPCION_SIN_SESION) $this->validarSesion();
  }
  //Obtener el arreglo de valores de los parámetros enviados
  private function obtenerParametros(Request $request) : array {
    $params = $request->input();
    $valores = [];
    foreach(array_keys($params) as &$key) {
      $valor = $params[$key];
      if(str_starts_with($key, "fecha")) {
        if($params[$key]== "") {
          $valor = null;
        } else {
          $fecha = (new DateTime());
          $fecha->setDate($valor['year'], $valor['month'], $valor['day']);
          $valor = $fecha;
        }
      } else if ($valor === "") {
        $valor = null;
      }
      $valores += [strtolower($key) => $valor];// $valor;
    }
    return $valores;  
  }
  //Validar que la sesión se encuentre activa para ejecutar operaciones BD
  private function validarSesion() : void{
    
    //ontener el usuario
    $this->usuarioID= $this->obtenerRegistro(QUERY_SEG::_SES_VALIDAR,
                            array("sesion" => $this->variablesServidor["Authorization"], 
                                  "ip" => $this->variablesServidor["ip"], 
                                  "opcion" => $this->opcion)
                            )->fnusr_validarsesion;

    if($this->usuarioID == 0) //Generar error de sesión si se valida que esta no existe
        throw new Exception("Sesión no activa o con IP diferente. Por favor autentíquese nuevamente o inicie nuevamente el registro");
  }
  //Eliminar algunos parámetros del request
  public function listarParamRequeridos(array $listaExcluir = null){
    $params = $this->parametros; //Se copian los parámetros
    if(!($listaExcluir == null)) {
      foreach($listaExcluir as $campo) {
        unset($params[$campo]); //Se excluyen los enviados
      }
    }
    return $params;
  }
  //Obtener resultados
  public function obtenerResultset(string $consulta, array $parametros = [], bool $incluirUsuario = false) {
    if(count($parametros) == 0) $parametros = $this->parametros;
    if($incluirUsuario) 
      $parametros += ["usuario" => $this->usuarioID];

    $rta = DB::select($consulta, $parametros);
    return $rta;
  }
  //Obtener registro base de datos
  public function obtenerRegistro(string $consulta, array $parametros = [], bool $incluirUsuario = false)  {
    if(count($parametros) == 0) $parametros = $this->parametros;
    if($incluirUsuario) 
      $parametros += ["usuario" => $this->usuarioID];

    $rta = DB::selectOne($consulta, $parametros);
    return $rta;
  }
  //Ejecutar consulta sin retorno
  public function actualizarData(string $consulta, array $parametros = [], bool $incluirUsuario = false) : int {
    if(count($parametros) == 0) $parametros = $this->parametros;
    if($incluirUsuario) 
      $parametros += ["usuario" => $this->usuarioID];

    $rta = DB::update($consulta, $parametros);
    return $rta;  
  }
  //Insertar auditoria
  public function insertarAuditoria(int $tipoAuditoria, string $descripcion, bool $exitoso, 
                                    string $operacion, string $observacion = null) : void {
    $this->actualizarData(QUERY_SEG::_AUD_INSERTAR, 
              array ( "tipoauditoria" => $tipoAuditoria,
                      "descripcion" => $descripcion, 
                      "observacion" => $observacion,
                      "exitoso" => $exitoso, 
                      "operacion" => $operacion), 
              true);
  }
}