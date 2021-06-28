<?php

namespace App\Models\Operacion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión de auditoría
class Contacto extends Core {
  //Variable de usuario
  public $usuario;

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Obtener el contacto por su id
  function obtenerContactoPorID() {
    //Obtener contacto por id
    $rta = $this->obtenerResultset("SELECT ctt_id id, ctt_nombre nombre, ctt_cargo cargo, ctt_direccion direccion,
    mnc_id municipioID, ctt_telefono telefono, ctt_email email
    from oper.ctt_contacto u where u.cct_id = :id");
  } 
  //Establece el contacto y retorna el número de creación
  public function establecerContacto(){

    if($this->parametros["id"]== 0) {//Insertar

      return $this->obtenerResultset("INSERT INTO oper.ctt_contacto(ctt_id, ctt_nombre, ctt_cargo, ctt_direccion, mnc_id, 
        ctt_telefono, ctt_email) VALUES (nextval('seg.seqcct'), :nombre, :cargo, :direccion, :municipioid, 
        :telefono, :email) RETURNING ctt_id");

    } else { //Actualizar
      $this->actualizarData("UPDATE seg.cct_contacto SET ctt_nombre: , ctt_cargo : cargo, ctt_direccion: direccion,
         mnc_id : municipioid, ctt_telefono : telefono, ctt_email: email WHERE cct_id =  :id");
      return $this->parametros["id"];
    }
  }
}
