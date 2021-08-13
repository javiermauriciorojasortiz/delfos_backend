<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Tarea;
use Exception;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class TareaController extends Controller {
  //Consulta lista de tareas
  function listarTareas(Request $request){
    $Tarea = new Tarea($request, ENUM_OPC::MIS_TAREAS);
    return $Tarea->listarTareas();
  }
}