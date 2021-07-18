<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;

use App\Models\ENUM_AUD;
use App\Models\QUERY_OPER;
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
	function ObtenerPorID(int $id) {
		$rta = $this->db->obtenerRegistro(QUERY_OPER::_CSO_OBTENERXID, 
							array("id" => $id));
		return $rta;
	}
	//Obtener casos por responsable
	function ConsultarPorResponsableID(){
		$rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTARPORRPSID, $this->listarParamRequeridos(["identificacion"]), false);
		$Descripcion = "Consultar Mis Casos ". $this->parametros["identificacion"];
		$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'C');		
		return $rta;
	}
}