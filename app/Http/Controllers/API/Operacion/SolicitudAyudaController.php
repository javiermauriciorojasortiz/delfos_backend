<?php

namespace App\Http\Controllers\API\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Enum\ENUM_OPC;
use App\Models\Operacion\SolicitudAyuda;
use Exception;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class SolicitudAyudaController extends Controller {
  //Consulta lista de seguimientos por caso
  function listarPorCaso(Request $request){
    $Sol = new SolicitudAyuda($request, ENUM_OPC::CONSULTAR_CASO);
    return $Sol->listarPorCaso();
  }
  //Crear solicitud de ayuda
  function crearSolicitudAyuda(Request $request){
    try {
      $Sol = new SolicitudAyuda($request, ENUM_OPC::SOLICITAR_AYUDA);
      $id = $Sol->crearSolicitudAyuda();
      return array("codigo"=> 1, "descripcion" => "", "data" => $id);
    } catch(Exception $e) {
      return array("codigo" => 0, "descripcion" => $e->getMessage());
    }
  }
}