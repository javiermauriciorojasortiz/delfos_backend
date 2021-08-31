<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

//Clase de gestión de seguimientos
class Seguimiento extends APPBASE {
	//Constructor por defecto
	function __construct(Request $request, int $opcion) {
		parent::__construct($request, $opcion);
	}
	//Consultar x caso
	function listarPorCaso() {
		$rta = $this->obtenerResultset(QUERY_OPER::_SGM_LISTARXCASO);
		return $rta;
	}
	//Obtener diagnostico por id
	function obtenerDiagnosticoPorID(){
		$rta = $this->obtenerRegistro(QUERY_OPER::_DGN_OBTENERXID);
		return json_encode($rta);
	} 
	//Establecer diagnóstico
	function establecerDiagnostico(){
		$params = $this->parametros["diagnostico"];
		$existe = 0;	
		$rta = 0;
		$id = $params["id"];
		if($id > 0) $existe = $this->obtenerRegistro(QUERY_OPER::_DGN_EXISTE, $params)->existe;
		if($existe == 1) {
			$rta = $id;
		} else {
			unset($params["id"]);
			$rta = $this->obtenerRegistro(QUERY_OPER::_DGN_INSERTAR, $params, true)->dgn_id;
		}
		return $rta;
	}
	//Insertar seguimiento
	function establecerSeguimiento($diagnosticoid){
		$params = $this->listarParamRequeridos(["diagnostico","proximasevaluaciones"]);
		$params["diagnosticoid"] = $diagnosticoid;
		$rta = $this->obtenerRegistro(QUERY_OPER::_SGM_INSERTAR, $params, true)->sgm_id;
		return $rta;
	}
	//Establecer proximas evaluaciones
	function establecerProximasEvaluaciones($seguimientoid){
		$params = $this->parametros["proximasevaluaciones"];
		foreach($params as $valor){
			$paramin = array(	
					"seguimientoid" => $seguimientoid,
					"categoriaid" => $valor["categoriaid"],
					"tipoatencionid" => $valor["tipoatencionid"],
					"fecha" => $valor["fecha"]);
			$this->actualizarData(QUERY_OPER::_PXE_INSERTAR, $paramin);
		}
	}
	//Listar próximas evaluaciones por seguimiento
	function listarProximasEvaluaciones() {
		$rta = $this->obtenerResultset(QUERY_OPER::_PXE_LISTARXSEGUIMIENTO);
		return $rta;
	}
	//
	function listarSeguimientosProximosPorCaso(){
		$rta = $this->obtenerResultset(QUERY_OPER::_PXE_LISTARXCASO);
		return $rta;
	}
	//Obtener seguimiento por id
	function obtenerSeguimientoPorID(){
		$rta = $this->obtenerRegistro(QUERY_OPER::_SGM_OBTENERXID);
		return $rta;	
	}
	//Establecer valoración
	function establecerConfirmacionSeguimiento(){
		$rta = $this->actualizarData(QUERY_OPER::_SGM_CONFIRMAR);
		return $rta;
	}
	//obtenerProximoSeguimientoPorID
	function obtenerProximoSeguimientoPorID(){
		$rta = $this->obtenerRegistro(QUERY_OPER::_PXE_OBTENERXID);
		return $rta;	
	}
	//confirmar la Programacion de la Proxima Evaluacion por parte del Responsable
	function confirmarProgramacionProximaEvaluacion(){
		$rta = $this->obtenerRegistro(QUERY_OPER::_PXE_CONFIRMARPROGRAMACION, $this->listarParamRequeridos(), true);
		return $rta;	
	}
}