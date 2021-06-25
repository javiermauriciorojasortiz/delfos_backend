<?php

namespace App\Core;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

//Clase de gestión del usuario
class Core {
    //Obtener el arreglo de valores de los parámetros enviados
    public function obtenerParametros(Request $request) {
      $params = $request->input(); //["descripcion", "email", "fechaFin", "fechaIni", "tipoAuditoria", "usuario"];
      //throw new Exception(implode("|",array_keys($params)) . '------' . implode("|",$params));

      $valores = [];
      foreach(array_keys($params) as &$key) {
          $valor = $params[$key];
          //throw new Exception("el puto valor " . $valor);
          if(str_starts_with($key, "fecha"))
          {
               if($params[$key]== "") {
                  $valor = null;
              } else {
                  $fecha = (new DateTime());
                  $fecha->setDate($valor['year'], $valor['month'], $valor['day']);
                  $valor = $fecha;
              }
          } else if ($valor === "") {
              throw new Exception("nulifico el valor");
              $valor = null;
          }
          //throw new Exception(strtolower($key) . ":" . $valor);
          $valores[strtolower($key)] = $valor;// $valor;
      }
      //throw new Exception(count($valores));
      return $valores;  }
}