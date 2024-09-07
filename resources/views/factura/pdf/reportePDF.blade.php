<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lista de Facturas</title>
    <style>
        @page {
            margin: 0cm 0cm;
            font-family: Arial;
        }

        body {
            margin: 3cm 1cm 1cm;
        }

        header {
            position        : fixed;
            top             : 1cm;
            left            : 1cm;
            right           : 1cm;
            height          : 2cm;
            background-color: #ffffff;
            color           : black;
            text-align      : center;
            line-height     : 50px;
        }

        footer {
            position        : fixed;
            bottom          : 0cm;
            left            : 1cm;
            right           : 1cm;
            height          : 2cm;
            background-color: #fff;
            color           : black;
            text-align      : center;
            line-height     : 35px;
        }

        .bordes {
            /* border: #24486C 1px solid; */
            border: 1px solid;
            border-collapse: collapse;
        }

        table.celdas {
            width: 100%;
            background-color: #fff;
            /* border: 1px solid; */
            border-collapse: collapse;
        }

        .celdas th {
            height: 10px;
            background-color: #E0E0E0;
            /* color: #fff; */
        }

        .celdas td {
            height: 12px;
        }

        .celdas th, .celdas td {
            border    : 1px solid black;
            padding   : 2px;
            text-align: center;
        }

        .celdabg {
            /* background-color: #E1ECF4; */
            background-color: #ffffff;
        }

    </style>
</head>
<body>
    <header>
        <table style="width:100%">
            <tr>
                <td style="text-align:center; font-family: 'Times New Roman', Times, serif; font-size:20px; line-height:100%">
                    <strong>{{ $empresa->nombre }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>LISTA DE EMISION DE FACTURAS</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; font-family: 'Times New Roman', Times, serif; font-size:15px; line-height:100%">
                    <strong>{{ date('d/m/Y H:i:s') }}</strong>
                </td>
            </tr>
        </table>
        {{-- <hr>
        <table style="width:100%">
            <tr>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>Carrera:</strong>
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    nombre
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>Paralelo:</strong>
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    PARALELO paralelo
                </td>
            </tr>
        </table> --}}
        {{-- <hr>
        <table style="width:100%">
            <tr>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>Curso:</strong>
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    gestion
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>Turno:</strong>
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    descripcion
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    <strong>Fecha:</strong>
                </td>
                <td style="text-align:left; font-family: 'Times New Roman', Times, serif; font-size:12px; line-height:100%">
                    fecha
                </td>
            </tr>
        </table> --}}
        <hr>
    </header>
    <main>
        <table cellpadding="1" class="celdas" style="font-family: 'Times New Roman', Times, serif; font-size:10px; text-align:center">
            <tr>
                <th>N°</th>
                <th>CLIENTE</th>
                <th>RAZON SOCIAL</th>
                <th>NIT</th>
                <th>FECHA</th>
                <th>MONTO</th>
                <th>SECTOR</th>
                <th>MODALIDAD</th>
                <th>ESTADO</th>
            </tr>
            @foreach ( $facturas as $fac)
                <tr>
                    <td>
                        @if ($fac->uso_cafc == "Si")
                            <span class="text-primary">N° Cafc: </span>{{ $fac->numero_cafc }}
                        @else
                            {{ $fac->numero_factura }}
                        @endif
                    </td>
                    <td>{{ $fac->nombres." ".$fac->ap_paterno." ".$fac->ap_materno }}</td>
                    <td>{{ $fac->razon_social }}</td>
                    <td>{{ $fac->nit }}</td>
                    <td>{{ $fac->fecha }}</td>
                    <td>{{ $fac->total }}</td>
                    <td>
                        @if ($fac->siat_tipo_documento_sector->codigo_clasificador == "8")
                            Fac. Tasa Cero
                        @else
                            Fac. Com. Venta
                        @endif
                    </td>
                    <td>
                        @if ($fac->tipo_factura == 'offline')
                            Fuera Linea
                        @else
                            Linea
                        @endif
                    </td>
                    <td>
                        @if (!is_null($fac->estado))
                            {{ $fac->estado }}
                        @else
                            Vigente
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
