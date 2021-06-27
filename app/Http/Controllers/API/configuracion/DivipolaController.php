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
}