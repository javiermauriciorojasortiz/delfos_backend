<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\Contacto;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class ContactoController extends Controller
{
  //Consulta lista de catálogos disponible en el sistema
  function obtenerContactoPorID(Request $request){
    $contacto = new Contacto($request, ENUM_OPC::OPCION_GENERAL);
    return $contacto->obtenerContactoPorID();
  }
}
