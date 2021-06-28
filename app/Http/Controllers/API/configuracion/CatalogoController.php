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
  //Obtener Valores catálogo por código
  function obtenerValoresporCodigoCatalogo(Request $request) {
    $catalogo = new Catalogo($request, 0);
    return $catalogo->obtenerValoresporCodigoCatalogo(); 
  }
  //Elimina el valor de un catálogo
  function eliminarValorCatalogo(Request $request) {
    $catalogo = new Catalogo($request, 0);
    return $catalogo->eliminarValorCatalogo(); 
  }
  //Establece el valor del catalogo y retorna el número
  function establecerValorCatalogo(Request $request){
    $catalogo = new Catalogo($request, 0);
    return $catalogo->establecerValorCatalogo(); 
  }
}