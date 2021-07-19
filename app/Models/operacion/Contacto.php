<?php

namespace App\Models\Operacion;

use App\Models\Core;
use App\Models\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión de auditoría
class Contacto extends Core {
  //Variable de usuario
  public $usuario;

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Obtener el contacto por su id
  function obtenerContactoPorID() {
    //Obtener contacto por id
    return json_encode($this->obtenerResultset(QUERY_OPER::_CTT_OBTENERXID)[0]);
  } 
  //Establece el contacto y retorna el número de creación
  public function establecerContacto(array $argumentos){
    //throw new Exception(implode("|", $params));
    if($argumentos["id"]==0) {//Insertar

      return $this->obtenerResultset(QUERY_OPER::_CTT_INSERTAR, $argumentos, false, ["id","departamentoid"])[0]->ctt_id;
    } else { //Actualizar

      $this->actualizarData(QUERY_OPER::_CTT_ACTUALIZAR, $argumentos, false, ["departamentoid"]);
      return $argumentos["id"];
    }
  }
  //Eliminar contacto
  function eliminarContacto(int $id){
    $rta = $this->actualizarData(QUERY_OPER::_CTT_ELIMINAR, ["id" => $id]);
  }
}
