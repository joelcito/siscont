<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style type="text/css">
          body{
            background-color: #ffffff; /* Color plomo */
          }

          .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
          }

          .line {
            margin-bottom: 10px; /* Espacio entre las líneas */
            padding: 10px;
            text-align: justify;
            font-weight: bold;
          }
          .titulo{
            font-size: 25px;
            color: #09366f;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;

          }

          .hijo{
            width: 500px;
            background-color: #ffffff;
            padding: 25px;
            border: 10px solid #f3451a; /* Borde azul */
            box-shadow: 0 0 0 10px #d7d7d7; /* Borde plomo (sombra) */
          }
          .tituloFooter{
            font-size: 25px;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            text-align: center
          }
    </style>
</head>
<body>
    <div class="container">
        <div class="hijo">
            <div class="line">
                <center>
                    <img src="{{ $logo }}" alt="aqui va el Logo " width="50%">
                </center>
            </div>
            <div class="titulo">
                {{ $empresa }}
            </div>
            <hr>
            <div class="line">
                Hola {{ $name }}
            </div>
            <div class="line">
                ¡Gracias por confiar en nosotros!
                Este correo es para informarle que la factura que emitio en la fecha {{ $date }} con numero
                N° {{ $number }} fue <b>ANULADA</b>
            </div>
            <div class="line">
                Cualquier consulta respecto a la factura, no dudes
                realizarla dentro del mes de su emisión y a través
                de nuestros canales de contacto.
            </div>
            <div class="line">
                ¡Que tengas un gran día!
            </div>
            <div class="line">
                Desarrollado por <a href="https://infinitassoluciones.net">Infinitas Soluciones</a>
            </div>
        </div>
        <br>
        <hr>
        <br>
    </div>
</body>
</html>
