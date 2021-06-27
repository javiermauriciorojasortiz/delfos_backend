<?php

namespace App\Models\Configuracion;

use App\Models\Core;
use Illuminate\Http\Request;

//Clase de gestión del usuario
class Parametro extends Core{

  //Construye el modelo
  function __construct(Request $request, int $opcion) {
    parent::__construct($request, $opcion);
  }
  //Consultar la lista de parámetros generales de la base de datos
  function consultarParametros() {
    return $this->obtenerResultset("SELECT prg_id id, prg_codigo codigo, prg_nombre nombre,
                t.tpd_id, t.tpd_nombre tipodatonombre, t.tpd_expresion tipodatoexpresion, t.tpd_explicacion tipodatoexplicacion,
                prg_valor valor,
                usr_primer_nombre || ' ' || usr_primer_apellido || ' ' || to_char(p.prg_fecha_auditoria, 'YYYY-MM-DD HH:MI:SSPM') auditoria
              from conf.prg_parametro_general p
              inner join conf.tpd_tipo_dato t on t.tpd_id = p.tpd_id 
              left join seg.usr_usuario u on u.usr_id = p.usr_id_auditoria
              where LOWER(prg_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'
      and LOWER(prg_nombre) like '%' || LOWER(coalesce(:nombre,'')) || '%'");
  }
  //Establece un cambio en el valor del parámetro
  function establecerParametro() {
    return $this->actualizarData("UPDATE conf.prg_parametro_general set prg_valor = :valor, usr_id_auditoria = :usuario,
          prg_fecha_auditoria = current_timestamp where prg_id=:id",null, true);
  }
  //Obtener un parámetro por su código()
  function obtenerParametroporCodigo(){
    return $this->obtenerResultset("SELECT prg_id id, prg_codigo codigo, prg_nombre nombre, prg_valor valor 
          from conf.prg_parametro_general p where LOWER(prg_codigo) like '%' || LOWER(coalesce(:codigo,'')) || '%'");  
  }
}