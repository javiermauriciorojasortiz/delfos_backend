<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión de auditoría
class Caso extends APPBASE {
	//Constructor por defecto
	function __construct(Request $request, int $opcion) {
		parent::__construct($request, $opcion);
	}
	//Consultar caso
	function ConsultarPorIdentificacion() {
		$Identificacion = $this->parametros["identificacion"];
		$rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTARXIDENTIFICACION,
						array("identificacion" => $Identificacion)
			);
		$Descripcion = "Consultar Caso ". $Identificacion;
		$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, (Count($rta)>0), 'C');
		return $rta;
	}
	//Obtener caso por id
	function ObtenerPorID() {
		$params = $this->listarParamRequeridos();
		$rta = $this->db->obtenerRegistro(QUERY_OPER::_CSO_OBTENERXID, $params);
		$Descripcion = "Consultar Caso ". $rta->identificacion .
									 ". Nombre " . $rta->primer_nombre . ' ' . $rta->primer_apellido;
		$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'C');	
		return $rta;
	}
	//Obtener casos por responsable
	function ConsultarPorResponsableID(){
		$rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTARPORRPSID, $this->listarParamRequeridos(["identificacion"]), false);
		$Descripcion = "Consultar Mis Casos. Responsable ". $this->parametros["identificacion"];
		$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'C');		
		return $rta;
	}
	//Obtener estados del paciente
	function listarEstadosPaciente() {
		return $this->obtenerResultset(QUERY_OPER::_ESP_LISTAR);
	}
	//Establecer  paciente
	function establecerPaciente(){
		$rta = null;
		if($this->parametros["id"]==0 ){
			$rta = $this->obtenerRegistro(QUERY_OPER::_CSO_INSERTAR, $this->listarParamRequeridos(["id","activo"]), true)->cso_id;
			$Descripcion = "Paciente ID: " . $rta . " . Identificacion: ". $this->parametros["identificacion"] . 
										 "Nombre: ". $this->parametros["primer_nombre"] . " " . $this->parametros["primer_apellido"] ;
			$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'I');	
		} else {
			$rta = $this->parametros["id"];
			$this->actualizarData(QUERY_OPER::_CSO_ACTUALIZAR, $this->listarParamRequeridos(["activo"]), true);
			$Descripcion = "Paciente ID: " . $rta . " . Identificacion: ". $this->parametros["identificacion"] . 
										 "Nombre: ". $this->parametros["primer_nombre"] . " " . $this->parametros["primer_apellido"] ;
			$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'M');		
		}
		return $rta;
	}
}