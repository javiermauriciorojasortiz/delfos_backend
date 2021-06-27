<?php

namespace App\Http\Controllers\API\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\EAPB;
use Illuminate\Http\Request;

//Controlador de EAPBs
class EAPBController extends Controller
{
  //Expone la consulta de EAPBs
  function consultarEAPBs(Request $request){
    $EAPB = new EAPB($request, 13);
    return $EAPB->consultarEAPBs();
  }
}
