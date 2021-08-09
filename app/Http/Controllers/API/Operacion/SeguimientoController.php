<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Caso;
use App\Models\Operacion\Seguimiento;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class SeguimientoController extends Controller {
  //Consulta lista de seguimientos por caso
  function listarPorCaso(Request $request){
    $Seg = new Seguimiento($request, ENUM_OPC::CONSULTAR_CASO);
    return $Seg->listarPorCaso();
  }
}