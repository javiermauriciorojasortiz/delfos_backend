<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
<p>Saludos estimado solicitante</p>

<p>A trav&eacute;s del siguiente bot&oacute;n usted podr&aacute; registrarse en la aplicaci&oacute;n e 
    iniciar su participaci&oacute;n como nuevo integrante de nuestra familia.</p>
<center>
    <div style="padding: 15px; display: block; max-width: 200px; border-radius: 15px; border: 1px solid #5A208C; background-color: #5a208c; color: white;">
        <a style="color: white; font-size: 15px; font-weight: 500; font-family: Arial, sans-serif; text-decoration: none;" href='{{$correo["servidor"]}}registro-usuario/{{$correo["token"]}}'>Inicie su registro ac&aacute;</a></div>
    <p>En caso de alg&uacute;n inconveniente, copie el siguiente link en una ventana del navegador para acceder al registro<br /> 
    <a target="_blank" style="font-size:smaller;color:#5A208C" href='{{$correo["servidor"]}}registro-usuario/{{$correo["token"]}}'>
    {{$correo["servidor"]}}registro-usuario/{{$correo["token"]}}
    </a>
    <p>
    <img style="margin-top:15px;max-width: 90px;" src="{{asset('/images/iconoiniciodesesion.png')}}" alt="icono delfos">
    <div style="color: #5a208c; font-size: 30px;font-weight:300; font-family: Arial, sans-serif;">DELFOS</div>
</center>

</body>
</html>