<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Divipola;
use App\Models\Enum\ENUM_OPC;
use Illuminate\Http\Request;

//Controlador de divisiones políticas
class DivipolaController extends Controller
{
  //Consultar la lista de paises
  function listarPaises(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->listarPaises();
  }
  //Consultar la lista de paises
  function listarMunicipios(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->listarMunicipios();
  }
  //Consultar la lista de paises
  function listarZonas(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->listarZonas();
  }
  //Consultar la lista de paises
  function listarBarrios(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->listarBarrios();
  }
  //Expone la consulta de listas de divipolas
  function consultarDivipolas(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->consultarDivipolas();
  }
  //Lista de divipolas sin control de acceso
  function listarDivipolas(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->listarDivipolas();
  }
  //Eliminar un departamento
  function eliminarDivipola(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->eliminarDivipola();
  }
  function establecerDivipola(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->establecerDivipola();
  }
  function obtenerMunicipiosporIDDivipola(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->obtenerMunicipiosporIDDivipola();
  }
  //Obtener la lista de secretarias
  function obtenerSecretarias(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::OPCION_GENERAL);
    return $divipola->obtenerSecretarias();
  }
  //Insertar - actualizar municipio
  function establecerMunicipio(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->establecerMunicipio();
  }
  //Eliminar un municipio
  function eliminarMunicipio(Request $request){
    $divipola = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $divipola->eliminarMunicipio();
  }
  function obtenerZonasporIDMunicipio(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->obtenerZonasporIDMunicipio();
  }
  function establecerZona(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->establecerZona();
  }
  function eliminarZona(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->eliminarZona();
  }
  function obtenerBarriosporIDZona(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->obtenerBarriosporIDZona();
  }
  function eliminarBarrio(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->eliminarBarrio();
  }
  function establecerBarrio(Request $request){
    $catalogo = new Divipola($request, ENUM_OPC::DEPARTAMENTO);
    return $catalogo->establecerBarrio();
  }
}