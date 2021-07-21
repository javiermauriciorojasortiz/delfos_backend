<?php

namespace App\Models\Seguridad;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_SEG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Clase de gestión de auditoría
class Auditoria extends APPBASE {
  //Variable de usuario
  public $usuario;

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar auditoría
  function consultarAuditoria() {
    $rta = $this->obtenerResultset(QUERY_SEG::_AUD_CONSULTAR);
    $observacion = "Consulta General de Auditoría";
    $this->insertarAuditoria(ENUM_AUD::AUDITORIA, $observacion, true, "C", ""); //Existe el usuario
    return $rta;
  }
  //Obtener tipos de auditoría
  function obtenerTiposAuditoria() {
    $rta = $this->obtenerResultset(QUERY_SEG::_TPA_CONSULTAR);
    return $rta;
  }
  //Insertar auditoría desde cualquier operación realizada
  function insertar(int $tipoAuditoria, string $descripcion, bool $exitoso, string $operacion, 
                    string $observacion = null) : int{
    $params = array ( "tipoauditoria" => $tipoAuditoria,
                      "descripcion" => $descripcion,
                      "observacion" => $observacion,
                      "usuario" => $this->usuarioID,
                      "exitoso" => $exitoso,
                      "operacion" => $operacion );
    return DB::update(QUERY_SEG::_AUD_INSERTAR,$params);
  }
}
