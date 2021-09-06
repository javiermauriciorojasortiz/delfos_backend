<?php

namespace App\Models\Operacion;

use App\Models\APPBASE;
use App\Models\Enum\ENUM_AUD;
use App\Models\Query\QUERY_OPER;
use Exception;
use Illuminate\Http\Request;

//Clase de gestión de reportes
class Reporte extends APPBASE {

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar casos de interés
  function consultarCasosInteres(){
    $rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTAR_CASOS_INTERES, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta casos de interés", true, "C");
    return $rta;
  }
  //Consultar reporte gerencial
  function consultarReporteGerencial(){
    $rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTAR_REPORTE_GERENCIAL, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta reporte gerencial", true, "C");
    return $rta;
  }
  //Consultar reporte geográfico
  function consultarReporteGeografico(){
    $rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTAR_REPORTE_GEOGRAFICO, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta reporte geográfico", true, "C");
    return $rta;
  }
  //Consultar tablero casos por EAPB
  function consultarCasosPorEAPB(){
    $rta = $this->obtenerResultset(QUERY_OPER::_TBC_CASOS_X_EAPB, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta tablero de control casos por EAPB", true, "C");
    return $rta;
  }
  //Consultar tablero casos por Estado
  function consultarCasosPorEstado(){
    $rta = $this->obtenerResultset(QUERY_OPER::_TBC_CASOS_X_ESTADO, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta tablero de control casos por estado", true, "C");
    return $rta;
  }
  //Consultar tablero casos por estado alerta
  function consultarEstadoAlertaCasos(){
    $rta = $this->obtenerResultset(QUERY_OPER::_TBC_CASOS_ESTADO_ALERTA, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta tablero de control casos por estado alerta", true, "C");
    return $rta;
  }
  //Consultar casos de interés tablero
  function consultarCasosInteresTablero(){
    $rta = $this->obtenerResultset(QUERY_OPER::_CSO_CONSULTAR_CASOS_INTERES_TABLERO, $this->listarParamRequeridos(), true);
    $this->insertarAuditoria(ENUM_AUD::REPORTES, "Consulta casos interés desde tablero  de control", true, "C");
    return $rta;
  }
}