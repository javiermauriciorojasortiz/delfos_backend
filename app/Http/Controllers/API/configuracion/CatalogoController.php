<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Catalogo;
use Illuminate\Http\Request;

//Clase controladora de los catálogos
class CatalogoController extends Controller
{
  //Consulta lista de catálogos disponible en el sistema
  function consultarCatalogos(Request $request){
    $catalogo = new Catalogo($request, 0);
    return $catalogo->consultarCatalogos();
  }
}