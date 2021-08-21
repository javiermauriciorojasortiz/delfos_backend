<?php

namespace App\Models\Operacion;

use App\Mail\msgUsuario;
use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_SEG;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

//Clase de gestión de auditoría
class Comunicacion extends APPBASE {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Enviar correos de comunicaciones
  function enviarCorreos() {
    //Obtener contacto por id
    $destinos = $this->obtenerResultset(QUERY_SEG::_TUS_DESTINO_COMUNICACIONES, 
                                          array("tiposusuario"=>$this->parametros["tiposusuario"]));
    $formatomensaje = $this->parametros["textomensaje"];
    $formatosubject = $this->parametros["temamensaje"];
    $errores = "";
    $enviados = 0;
    $rta = null;
    foreach ($destinos as $destino){
      try {
        $textomensaje = str_replace("{{nombre}}", $destino->nombre, $formatomensaje);
        $textomensaje = str_replace("{{tipoentidad}}", $destino->tipoentidad, $textomensaje);
        $textomensaje = str_replace("{{entidad}}", $destino->entidad, $textomensaje);
        $textotema = str_replace("{{nombre}}", $destino->nombre, $formatosubject);
        $textotema = str_replace("{{tipoentidad}}", $destino->tipoentidad, $textotema);
        $textotema = str_replace("{{entidad}}", $destino->entidad, $textotema);
        $mensaje["texto"] = $textomensaje;
        Mail::to($destino->email)->send(new msgUsuario($mensaje, $textotema, 'mails.correoGeneral'));
        $enviados += 1;
      } catch (Exception $e) {
        $errores = $errores . $e->getMessage() . "<br>";
      }
    }
    $observacion = "Se enviaron (" . $enviados . ") correos de (" . count($destinos) . ") encontrados. " . $errores;
    $rta = array("codigo" => strlen($errores)>0?0:1, 
      "descripcion" => $observacion);
    $this->insertarAuditoria(ENUM_AUD::COMUNICACIONES, "Envio masivo correos", $enviados>0?true:false, "G", 
      strlen($observacion)>255?substr($observacion, 0, 255):$observacion);
    return $rta;
  }
  //Ejecutar conta
  function enviarSMSs(){
    //Obtener contacto por id
    $destinos = $this->obtenerResultset(QUERY_SEG::_TUS_DESTINO_COMUNICACIONES, 
                        array("tiposusuario"=>$this->parametros["tiposusuario"]));
    $formatomensaje = $this->parametros["textomensaje"];
    foreach ($destinos as $destino){
      $textomensaje = str_replace("{{nombre}}", $destino->nombre, $formatomensaje);
      $textomensaje = str_replace("{{tipoentidad}}", $destino->tipoentidad, $textomensaje);
      $textomensaje = str_replace("{{entidad}}", $destino->entidad, $textomensaje);
      //Mail::to($destino->email)->send(new msgUsuario($mensaje, $textotema, 'mails.correoGeneral'));
    }
    throw new Exception("No implementado");
    return count($destinos);
  }
}