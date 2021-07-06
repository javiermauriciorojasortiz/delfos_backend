<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Divipola;
use Illuminate\Http\Request;

//Controlador de divisiones políticas
class DivipolaController extends Controller
{
  //Expone la consulta de listas de divipolas
  function consultarDivipolas(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->consultarDivipolas();
  }
  function listarDivipolas(Request $request){
    $catalogo = new Divipola($request, 0);
    return $catalogo->listarDivipolas();
  }
  function eliminarDivipola(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->eliminarDivipola();
  }
  function establecerDivipola(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->establecerDivipola();
  }
  function obtenerMunicipiosporIDDivipola(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->obtenerMunicipiosporIDDivipola();
  }
  function obtenerSecretarias(Request $request){
    $catalogo = new Divipola($request, 0);
    return $catalogo->obtenerSecretarias();
  }
  function establecerMunicipio(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->establecerMunicipio();
  }
  function eliminarMunicipio(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->eliminarMunicipio();
  }
  function obtenerZonasporIDMunicipio(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->obtenerZonasporIDMunicipio();
  }
  function establecerZona(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->establecerZona();
  }
  function eliminarZona(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->eliminarZona();
  }
  function obtenerBarriosporIDZona(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->obtenerBarriosporIDZona();
  }
  function eliminarBarrio(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->eliminarBarrio();
  }
  function establecerBarrio(Request $request){
    $catalogo = new Divipola($request, 12);
    return $catalogo->establecerBarrio();
  }
}