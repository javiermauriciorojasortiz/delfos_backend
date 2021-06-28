<?php

namespace App\Http\Controllers\API\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\Auditoria;
use Illuminate\Http\Request;
//Controlador de auditoría
class AuditoriaController extends Controller
{
    //Consultar la auditoria {tipoAuditoria: 0, usuario: "", email: "", descripcion: "", fechaIni: "", …}
    public function consultarAuditoria(Request $request){
        $auditoria = new Auditoria($request, 0);
        return $auditoria->consultarAuditoria();
    }
    //Obtener la lista de los tipos de auditoría
    public function obtenerTiposAuditoria(Request $request){
        $auditoria = new Auditoria($request, 0);
        return $auditoria->obtenerTiposAuditoria();
    }
}
