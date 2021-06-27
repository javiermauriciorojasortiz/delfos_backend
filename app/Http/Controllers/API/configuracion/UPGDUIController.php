<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\UPGDUI;
use Illuminate\Http\Request;

//Controlador de UPGD
class UPGDUIController extends Controller
{
  //Expone la lista de UPGDUIs
  function consultarUPGDUIs(Request $request){
    $UPGDUI = new UPGDUI($request, 15);
    return $UPGDUI->consultarUPGDUIs();
  }
}
