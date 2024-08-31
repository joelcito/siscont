{{--  @extends('errors::minimal')  --}}

{{--  @section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))  --}}


{{--  @extends('layouts.app')  --}}

{{--  @extends('errors::minimal')
@section('content')
<div class="container text-center">
    <h1>404 - Página no encontrada</h1>
    <p>{{ session('error_message', 'Lo sentimos, pero la página que estás buscando no existe.') }}</p>
    <a href="{{ url('/') }}">Volver al inicio</a>
</div>
@endsection  --}}


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema</title>
    <style>
        #outer {
            width: 100%;
            text-align: center;
            padding-top: 50px;
        }

        #inner {
            display: inline-block;
            width: 50%;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18pt;
            background-color: #FDFBFE;
        }

        .button {
            background-color: #008CBA;
            /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .center {
            margin: auto;
            width: 50%;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div id="outer">
        <div id="inner">
            <img src="{{ asset('assets/img/diseno-web-404-CSS.webp') }}" alt="">
            <p>
                Pagina no Encontrada! <br><br>
                <a href="{{ url()->previous() }}" class="button">REGRESAR</a>
            </p>

        </div>
    </div>
</body>

</html>
