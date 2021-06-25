<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seguridad\UsuarioModel;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\DB;
use App\Core\Seguridad\Usuario;
use App\Core\Seguridad\respuestaAPI;
use App\Mail\Usuario as MailUsuario;
use DateTime;
use Exception;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UsuarioModel::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $pass)
    {
        $usuario = UsuarioModel::where('usr_identificacion', '=', $id)
            ->where('usr_clave', '=', $pass)
            ->get();

        if (count($usuario) > 0) {
            return $usuario;
            // response()->json([
            //     'res' => true,
            //     'usuario' => $usuario,
            // ]);
        } else {
            return  false;
            // response()->json([
            //     'res' => false,
            //     'usuario' => $usuario,
            // ]);
        }
    }
    public function buscar(Request $request){//, int $id) {

        // DB::beginTransaction();

        return $request->input()["id"];
        try {
            $usuario = $this->obtenerUsuarioSesion($request);
            return $usuario->obtenerMenuUsuario();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    //Forma genérica de obtener el usuario de sesión
    private function obtenerUsuarioSesion(Request $request){
        $sesion = $request->header("Authorization");
        return new Usuario($sesion);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
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
        return $valores;//["tipoAuditoria"=>null, "usuario" =>null, "email"=>null, "descripcion"=>null, "fechaIni"=>null, "fechaFin" => null ];//$valores;
    }

    //Consultar la auditoria {tipoAuditoria: 0, usuario: "", email: "", descripcion: "", fechaIni: "", …}
    public function consultarAuditoria(Request $request){
        //throw new Exception($request);
        $usuario = $this->obtenerUsuarioSesion($request);
        $parametros = $this->obtenerParametros($request);
        return $usuario->consultarAuditoria($parametros);
    }
    //Lista de usuarios desde la consulta
    public function consultarUsuarios(Request  $request){
        $usuario = $this->obtenerUsuarioSesion($request);
        $parametros = $this->obtenerParametros($request);
        //throw new Exception(implode("|",$parametros));
        //throw new Exception($request);
        return $usuario->consultarUsuarios($parametros);
    }

    public function autenticar(Request $request) {
        $params = $this->obtenerParametros($request);
        $params["ip"] = $request->ip();
        $usuario = new Usuario();
        return $usuario->autenticar($params);;
    }
    //Autenticar al usuario por el token guardado en las cookies del cliente
    public function autenticarporToken(Request $request){
        $params = $this->obtenerParametros($request);
        $params["ip"] = $request->ip();
        $usuario = new Usuario();
        return $usuario->autenticarporToken($params);;
    }
    //Enviar correo 
    public function enviarcorreo(Request $request){
        $rta = Array(); //new respuestaAPI();
        $rta["codigo"] = 0;

        DB::beginTransaction();
        try {
            $params = $this->obtenerParametros($request);
            $params["ip"] = $request->ip();
            $usuario = new Usuario();
            $data = $usuario->enviarCorreo($params);
            $rta["codigo"] = 1;
            $rta["descripcion"] = "Exitoso";
            $rta["data"] = $data;
            DB::commit();
        } catch(\Exception $ex) {
            DB::rollback();
            $rta["codigo"] = 0;
            $rta["descripcion"] = $ex->getMessage();
        }
        return $rta;
    }
    //Obtiene las opciones de menú del usuario
    public function obtenerMenuUsuario(Request $request){
        $usuario = $this->obtenerUsuarioSesion($request);
        return $usuario->obtenerMenuUsuario();
    }
//   //Obtener tipos de auditoria
//   public function obtenerTiposAuditoria(){
//     $usuario = $this->obtenerUsuarioSesion();
//     return $usuario->obtenerTiposAuditoria(Array($request));
//   }
//   //Eliminar usuario
//   public function eliminarUsuario(Request $request, int $id){
//     $usuario = $this->obtenerUsuarioSesion($id); 
//     return $usuario(`${this.endPoint}/eliminarusuario/id?${id}`);   
//   }
//   //Cambiar Clave
//   public function cambiarClave(forma : any){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<respuestaAPI>((observer) => {observer.next(listaRtaAPI[0]);});
//     return this.http.post<respuestaAPI>(`${this.endPoint}/cambiarclave`, forma);
//   }
//   //Enviar correo para autenticación
//   public function enviarCorreo(forma : any){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<respuestaAPI>((observer) => {observer.next(listaRtaAPI[0]);});
//     return this.http.post<respuestaAPI>(`${this.endPoint}/enviarcorreo`, forma);
//   }
//   //Autenticación de usuario
//   public function login(forma: any){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<Usuario>((observer) => {observer.next(listaUsr[0]);});
//     return this.http.post<Usuario>(`${this.endPoint}/login`, forma);
//   }
//   //Obtener usuario por id
//   public function obtenerUsuarioporID(id: number){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<Usuario>((observer) => {observer.next(listaUsr[0]);});
//     return this.http.get<Usuario>(`${this.endPoint}/obtenerusuario/id?${id}`);   
//   }

//   //Establece el usuario y retorna el número
//   public function establecerUsuario(usuario : Usuario){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<number>((observer) => {observer.next(1);});
//     return this.http.post<number>(`${this.endPoint}/establecerusuario`, usuario);
//   }
//   //Obtiene la lista de roles posibles de la base de datos
//   public function obtenerTiposUsuario() {
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<ptTipoUsuario[]>((observer) => {observer.next(listaTus);});
//     return this.http.get<ptTipoUsuario[]>(`${this.endPoint}/obtenertiposusuario`);
//   }
//   //Obtiene la lista de estados del usuario
//   public function obtenerEstadosUsuario(){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy)  return new Observable<ptEstadoUsuario[]>((observer) => {observer.next(listaEus);});
//     return this.http.get<ptEstadoUsuario[]>(`${this.endPoint}/obtenerestadosusuario`);   
//   }



}
