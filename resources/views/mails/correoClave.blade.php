<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Nueva Clave</title>
</head>
<body>
<p>Saludos estimado {{$correo["nombre"]}}</p>
<p>A su solicitud se envía la siguiente clave aleatoria</p>
<center>
    <div style="padding: 15px; display: block; max-width: 200px; border-radius: 15px; border: 1px solid #5A208C; background-color: #5a208c; color: white;">
        <span style="color: white; font-size: 15px; font-weight: 500; font-family: Arial, sans-serif; text-decoration: none;">{{$correo["clave"]}}</span></div>
    <p>Ingrese a nuestra aplicación a través del siguiente link e inicie sesión con esta nueva clave<br /> 
    <a target="_blank" style="font-size:smaller;color:#5A208C" href='{{$correo["servidor"]}}'>
    {{$correo["servidor"]}}
    </a>
    <p>
    <img style="margin-top:15px;max-width: 90px;" src="{{asset('/images/iconoiniciodesesion.png')}}" alt="icono delfos">
    <div style="color: #5a208c; font-size: 30px;font-weight:300; font-family: Arial, sans-serif;">DELFOS</div>
</center>

</body>
</html>