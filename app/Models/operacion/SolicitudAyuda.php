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
	//Guardar atención de la EPS en la solicitud
	function guardarAtencionSolicitud(){
		$rta = $this->actualizarData(QUERY_OPER::_ATP_ESTABLECERATENCION, $this->parametros, true);
		return $this->parametros["id"];
  }
	//Confirmar atención de recibida de la solicitud
	function confirmarAtencionSolicitud(){
		$rta = $this->actualizarData(QUERY_OPER::_ATP_ESTABLECERCONFIRMACION, $this->parametros, true);
		return $this->parametros["id"];
  }
	//Obtener solicitud por id
	function obtenerSolicitudPorID(){
		$rta = $this->obtenerRegistro(QUERY_OPER::_ATP_OBTENERXID);
		return $rta;
	}
}