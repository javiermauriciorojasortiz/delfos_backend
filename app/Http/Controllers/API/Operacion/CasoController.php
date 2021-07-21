<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Caso;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class CasoController extends Controller
{
  //Consulta lista de pacientes disponible en el sistema asociados a la identifidación
  function ConsultarPorIdentificacion(Request $request){
    $Caso = new Caso($request, ENUM_OPC::CONSULTAR_CASO);
    return $Caso->ConsultarPorIdentificacion();
  }
  //Consultar mi lista de pacientes en la que estoy como responsable
  function ConsultarPorResponsableID(Request $request){
    $Caso = new Caso($request, ENUM_OPC::MIS_PACIENTES);
    return $Caso->ConsultarPorResponsableID();
  }
  //Listar estados paciente
  function listarEstadosPaciente(Request $request) {
    $Caso = new Caso($request, ENUM_OPC::OPCION_GENERAL);
    return $Caso->listarEstadosPaciente(); 
  }
}
