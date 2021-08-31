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
	function __construct(Request $request, $opcion) {
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
		$rta = $this->obtenerRegistro(QUERY_OPER::_CSO_OBTENERXID, $params);
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
			$rta = $this->obtenerRegistro(QUERY_OPER::_CSO_INSERTAR, $this->listarParamRequeridos(["id","activo","responsableprincipal", "responsablesecundario"]), true)->cso_id;
			$Descripcion = "Paciente ID: " . $rta . " . Identificacion: ". $this->parametros["identificacion"] . 
										 "Nombre: ". $this->parametros["primer_nombre"] . " " . $this->parametros["primer_apellido"] ;
			$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'I');	
		} else {
			$rta = $this->parametros["id"];
			$this->actualizarData(QUERY_OPER::_CSO_ACTUALIZAR, $this->listarParamRequeridos(["activo","responsableprincipal", "responsablesecundario"]), true);
			$Descripcion = "Paciente ID: " . $rta . " . Identificacion: ". $this->parametros["identificacion"] . 
										 "Nombre: ". $this->parametros["primer_nombre"] . " " . $this->parametros["primer_apellido"] ;
			$this->insertarAuditoria(ENUM_AUD::CASO, $Descripcion, true, 'M');		
		}
		return $rta;
	}
	//Listar históricos paciente
	function listarHistoricoPaciente(){
		$rta = $this->obtenerResultset(QUERY_OPER::_CSOH_LISTAR);
		return $rta;
	}
	//Obtener la lista de responsables del caso
	function obtenerResponsablesxCaso(){
		$rta = $this->obtenerResultset(QUERY_OPER::_CSO_LISTARRESPONSABLES);
		return $rta;		
	}
	//Actualizar seguimiento
	function actualizarUltimoSeguimiento($seguimientoid, $casoid){
		$rta = $this->actualizarData(QUERY_OPER::_CSO_ACTUALIZAR_ULTIMO_SEGUIMIENTO, 
			array("seguimientoid"=> $seguimientoid, "casoid"=>$casoid));
		return $rta;		
	}
	//Establecer relacion responsable
	function establecerRelacionResponsable(int $idCaso, int $idUsuario, int $tiporelacionid, bool $principal){
		$relacion = $this->obtenerResultset(QUERY_OPER::_RPC_EXISTE, array("casoid"=>$idCaso, "responsableid"=>$idUsuario));
		$params = array("casoid"=>$idCaso, "responsableid"=>$idUsuario, "tiporelacionid"=>$tiporelacionid, "principal"=>$principal);
		if(count($relacion)>0){ //Existe y debe actualizarse
			$this->actualizarData(QUERY_OPER::_RPC_ACTUALIZAR, $params, true);
		} else { //Insertar relación
			$this->actualizarData(QUERY_OPER::_RPC_INSERTAR, $params, true);
		}
	}
	//Activa un caso inactivo
	function activarCaso(){
		$params = array("id" => $this->parametros["id"]);
		$paciente = $this->obtenerRegistro(QUERY_OPER::_CSO_ACTIVAR, $params, true);
		$Descripcion = "Activar Caso Paciente ID: " . $paciente->id . " . Identificacion: ". $paciente->identificacion . 
		"Nombre: ". $paciente->nombre ;
		$this->insertarAuditoria(ENUM_AUD::ACTIVAR_INACTIVAR_CASO, $Descripcion, true, 'M');			
	} 
	//Inactivar caso 
	function inactivarCaso(){
		$params = $this->parametros;
		$paciente = $this->obtenerRegistro(QUERY_OPER::_CSO_INACTIVAR, $params, true);
		$Descripcion = "Inactivar Caso Paciente ID: " . $paciente->id . " . Identificacion: ". $paciente->identificacion . 
		"Nombre: ". $paciente->nombre ;
		$this->insertarAuditoria(ENUM_AUD::ACTIVAR_INACTIVAR_CASO, $Descripcion, true, 'M');
	}
	//Lista de casos reportados inicialmente por el notificador
	function obtenerCasosNotificador(){
		return $this->obtenerResultset(QUERY_OPER::_CSO_OBTENERXNOTIFICADOR);
	}
}