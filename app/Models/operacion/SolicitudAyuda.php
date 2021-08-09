<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión de seguimientos
class SolicitudAyuda extends APPBASE {
	//Constructor por defecto
	function __construct(Request $request, int $opcion) {
		parent::__construct($request, $opcion);
	}
	//Consultar x caso
	function listarPorCaso() {
		$rta = $this->obtenerResultset(QUERY_OPER::_ATP_LISTARXCASO);
		return $rta;
	}
  //Iniciar la solicitud de ayuda
  function crearSolicitudAyuda(){
		$rta = $this->obtenerResultset(QUERY_OPER::_ATP_CREAR, $this->parametros, true)[0]->atp_id;
		return $rta;
    
  }
}