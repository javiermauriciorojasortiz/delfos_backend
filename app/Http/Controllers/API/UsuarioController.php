<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seguridad\UsuarioModel;
use PhpParser\Node\Expr\Empty_;
use Illuminate\Support\Facades\DB;
// include("App\Core\seguridad");
use App\Core\Seguridad\Usuario;
use DateTime;

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
        // foreach (UsuarioModel::all() as $cur) {
        //   array_push($rta,  $cur -> usr_id);
        // }
        // return $rta;
    }
    //Forma genérica de obtener el usuario de sesión
    private function obtenerUsuarioSesion(Request $request){
        $sesion = $request->header('token', 'cJnYmW6vgo1HPqr4WYR2RT8OgurEOwQXtriIHSx3mvEPeNxYx4');
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
        $valores = [];
        foreach(array_keys($params) as &$key) {
            $valor = $params[$key];
            if(str_starts_with($key, "fecha"))
            {
                 if($params[$key]== "") {
                    $valor = null;
                } else {
                    $fecha = (new DateTime());
                    $fecha->setDate($valor['year'], $valor['month'], $valor['day']);
                    $valor = $fecha;
                }
            } else if ($valor == "") {
                $valor = null;
            }
            $valores[$key] = $valor;// $valor;
        }
        return $valores;//["tipoAuditoria"=>null, "usuario" =>null, "email"=>null, "descripcion"=>null, "fechaIni"=>null, "fechaFin" => null ];//$valores;
    }

    //Consultar la auditoria {tipoAuditoria: 0, usuario: "", email: "", descripcion: "", fechaIni: "", …}
    public function consultarAuditoria(Request $request){
        $usuario = $this->obtenerUsuarioSesion($request);
        return $usuario->consultarAuditoria($this->obtenerParametros($request));
    }

    public function autenticar(Request $request) {
        $params = $this->obtenerParametros($request);
        $params["ip"] = $request->ip();
        $usuario = new Usuario();
        return $usuario->autenticar($params);;
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
//   //Lista de usuarios desde la consulta
//   public function consultaUsuarios(query : any){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<Usuario[]>((observer) => {observer.next(listaUsr);});
//     return this.http.post<Usuario[]>(`${this.endPoint}/consultarusuarios`, query);
//   }
//   //Obtener usuario por id
//   public function obtenerUsuarioporID(id: number){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<Usuario>((observer) => {observer.next(listaUsr[0]);});
//     return this.http.get<Usuario>(`${this.endPoint}/obtenerusuario/id?${id}`);   
//   }
//   //Obtiene las opciones de menú del usuario
//   public function obtenerMenuUsuario(){
//     $usuario = this->obtenerUsuarioSesion();

//     if(appUtilService.dummy) return new Observable<Opcion[]>((observer) => {observer.next(listaOpc);});
//     return this.http.get<Opcion[]>(`${this.endPoint}/obtenermenuusuario`);      
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
