<?php

namespace App\Models\Operacion;

use App\Models\Core;
use Exception;
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
    return json_encode($this->obtenerResultset("SELECT ctt_id id, ctt_nombre nombre, ctt_cargo cargo, ctt_direccion direccion,
    m.mnc_id municipioid, m.dvp_id departamentoid, ctt_telefono telefono, ctt_email email
    from oper.ctt_contacto u left join conf.mnc_municipio m on m.mnc_id = u.mnc_id where u.ctt_id = :id")[0]);
  } 
  //Establece el contacto y retorna el número de creación
  public function establecerContacto(array $argumentos){
    //throw new Exception(implode("|", $params));
    if($argumentos["id"]==0) {//Insertar

      return $this->obtenerResultset("INSERT INTO oper.ctt_contacto(ctt_id, ctt_nombre, ctt_cargo, ctt_direccion, mnc_id, 
        ctt_telefono, ctt_email) VALUES (nextval('oper.seqctt'), :nombre, :cargo, :direccion, :municipioid, 
        :telefono, :email) RETURNING ctt_id", $argumentos, false, ["id","departamentoid"])[0]->ctt_id;
    } else { //Actualizar

      $this->actualizarData("UPDATE oper.ctt_contacto SET ctt_nombre = :nombre, ctt_cargo = :cargo, ctt_direccion = :direccion,
         mnc_id = :municipioid, ctt_telefono = :telefono, ctt_email = :email WHERE ctt_id = :id", $argumentos, 
         false, ["departamentoid"]);
      return $argumentos["id"];
    }
  }
  //Eliminar contacto
  function eliminarContacto(int $id){
    $rta = $this->actualizarData("DELETE FROM oper.ctt_contacto where ctt_id = :id", ["id" => $id]);
  }
}
