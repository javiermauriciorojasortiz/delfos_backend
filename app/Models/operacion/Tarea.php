<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión de seguimientos
class Tarea extends APPBASE {
	//Constructor por defecto
	function __construct(Request $request, int $opcion) {
		parent::__construct($request, $opcion);
	}
	//Consultar lista de tareas
	function ListarTareas() {
		//throw new Exception($this->usuarioID);
		$rta = $this->obtenerResultset(QUERY_OPER::_TAR_LISTAR, array(), true);
		$this->insertarAuditoria(ENUM_AUD::TAREA, "Consultar Tareas", true, "C");
		return $rta;
	}
}