<?php

namespace App\Models\Seguridad;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_SEG;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Notificador extends APPBASE {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Obtener lista notificaciones
  function consultarNotificadores() {
    $rta = $this->obtenerResultset(QUERY_SEG::_NTF_CONSULTAR);
    return $rta;
    //$rta = DB::select('exec seg.pausr_obtenerporsesion(?)',array($params));
  }

  //Obtener usuario por id
  public function obtenerNotificador(){
    return $this->obtenerResultset(QUERY_SEG::_NTF_OBTENERXID)[0];
  }
  //Inserta o actualiza el notificador
  public function establecerNotificador(int $id, bool $nuevo){
    $params = array("id" => $id, "pregrado"  => $this->parametros["pregrado"], 
                    "registro_medico" => $this->parametros["registro_medico"],
                    "autoriza_email" => $this->parametros["autoriza_email"],
                    "autoriza_sms" => $this->parametros["autoriza_sms"],
                    "especialidadid" => $this->parametros["especialidadid"]);
    if($nuevo) {//Insertar
      //throw new Exception(implode("|", $params));
      $rta = $this->actualizarData(QUERY_SEG::_NTF_INSERTAR, $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria($this->usuarioID, ENUM_AUD::NOTIFICADOR, $observacion, true, "I", ""); //Existe el usuario
    } else { //Actualizar
      
      $rta = $this->actualizarData(QUERY_SEG::_NTF_ACTUALIZAR, $params, true);
      $observacion = "Usuario ID: " . $params["id"] . ". Identificacion: " . $this->parametros["identificacion"];
      $this->insertarAuditoria($this->usuarioID, ENUM_AUD::NOTIFICADOR, $observacion, true, "M", ""); //Existe el usuario  
    }
    return $rta;
  }

  //Establece el notificador y retorna el número
  public function validarNotificador(){
      return $this->actualizarData(QUERY_SEG::_NTF_VALIDAR, $this->listarParamRequeridos(), true);
  }
}