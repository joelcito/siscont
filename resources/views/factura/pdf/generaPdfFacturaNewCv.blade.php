<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FACTURA</title>
    <style type="text/css">
        @page {
            margin: 15px;
        }

        body {
            /* background-image: url('<?php //echo base_url(); ?>public/assets/images/reportes/formato.png'); */
            background-repeat: no-repeat;
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        .titulos {
            font-size: 18pt;
        }
        .subtitulos {
            font-size: 14pt;
        }

        /*estilos para tablas de datos*/
        table.datos {
            font-size: 11px;
            /*line-height:14psx;*/
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            position: absolute;
            top:250px;
            left: 25px;
            width: 720px;
        }

        .datos th {
            /* text-transform: uppercase; */
            height: 25px;
            background-color: #f5f5f5;
            color: #000000;
        }

        .datos td {
            font-size: 8pt;
            height: 20px;
        }

        .datos th,
        .datos td {
            border: 1px solid #000000;
            padding: 2px;
            /*text-align: center;*/
        }

        .datos tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /*fin de estilos para tablas de datos*/
        /*estilos para tablas de contenidos*/
        table.contenidos {
            /*font-size: 13px;*/
            line-height: 14px;
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        .contenidos th {
            height: 20px;
            background-color: #616362;
            color: #fff;
        }

        .contenidos td {
            height: 10px;
        }

        .contenidos th,
        .contenidos td {
            border-bottom: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        /*.contenidos tr:nth-child(even) {background-color: #f2f2f2;}*/
        /*fin de estilos para tablas de contenidos*/


        /*ESTILOS NUEVOS PARA LAS TABLAS */
        #table_casa_matriz{
            /*border: 1px solid ;*/
            position: absolute;
            width: 300px;
            margin-left: 20px;
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
        }

        #table_nit_num_fac{
            {{--  background-color: pink;  --}}
            position: absolute;
            max-width:100px;
            right: -20px;
            top: 20px;
            text-align: left;
            font-size: 11px;
        }

        #table_nuew_num_fac{
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 11px;
            width: 50px;
        }

        .estatico{
            width: 120px;
            height: 50px;
            word-wrap: break-word;
        }

        #TableFactura{
            position: absolute;
            top: 150px;
            left: 300px;

        }

        #table_datos_factura{
            position: absolute;
            width: 300px;
            margin-left: 20px;
            margin-top: 200px;
            text-align: center;
            font-size: 11px;
            text-align: left;
        }

        #table_datos_factura1{
            position: absolute;
            width:300px;
            right: -20px;
            top: 200px;
            font-size: 11px;
        }

        #anulado{
            position: absolute;
            font-size: 75px;
            color:rgb(227, 142, 142, 0.8);
            font-weight: bold;
            top: 35%;
            left: 20%;
            transform: rotate(-45deg);
            /*display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;*/
        }


    </style>
</head>

<body>

    <table id="table_casa_matriz">
        <thead>
            <tr>
                <th style="text-align: center;">
                    {{ $archivoXML->cabecera->razonSocialEmisor }}
                    <br>
                    CASA MATRIZ
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>No. Punto de Venta {{ $archivoXML->cabecera->codigoPuntoVenta }}</td>
            </tr>
            <tr>
                <td>
                    {{ $archivoXML->cabecera->direccion }}
                </td>
            </tr>
            <tr><td>Telefono: {{ $archivoXML->cabecera->telefono }}</td></tr>
            <tr><td>{{ $archivoXML->cabecera->municipio }}</td></tr>
        </tbody>
    </table>

    <table id="table_nuew_num_fac">
        <tr>
            <td><b>NIT</b></td>
            <td width="100px">{{ $archivoXML->cabecera->nitEmisor }}</td>
        </tr>
        <tr>
            <td><b>FACTURA N°</b></td>
            <td width="100px">{{ $archivoXML->cabecera->numeroFactura }}</td>
        </tr>
        <tr>
            <td><b>CÓD. AUTORIZACIÓN</b></td>
            <td>
                <div class="estatico">
                    {{ $archivoXML->cabecera->cuf }}
                </div>
            </td>
        </tr>
    </table>

    <table id="TableFactura">
        <thead>
            <tr>
                <th style="font-size: 12;">FACTURA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 8;">(Con Derecho a Crédito Fiscal)</td>
            </tr>
        </tbody>
    </table>

    <table id="table_datos_factura">
        <tbody>
            <tr>
                <td>
                   <b>Fecha:</b>
                </td>
                <td>
                    @php
                        $fechaHora = $archivoXML->cabecera->fechaEmision;
                        $dateTime = new DateTime($fechaHora);
                        $formattedDateTime = $dateTime->format('d/m/Y h:i A');
                    @endphp
                    {{ $formattedDateTime }}
                </td>
            </tr>
            <tr>
                <td><b>Nombre/Razón Social:</b></td>
                <td>{{ $archivoXML->cabecera->nombreRazonSocial }}</td>
            </tr>
        </tbody>
    </table>

    <table id="table_datos_factura1">
        <thead>
            <tr>
                <td style="text-align: right">
                   <b>NIT/CI/CEX:</b>
                </td>
                <td>
                    {{ $archivoXML->cabecera->numeroDocumento }}
                    @if (!empty($archivoXML->cabecera->complemento))
                        - {{ $archivoXML->cabecera->complemento }}
                    @endif

                    {{--  @dd(
                        $archivoXML->cabecera->complemento,
                        isset($archivoXML->cabecera->complemento) ,
                        count($archivoXML->cabecera->complemento->children()) > 0,
                        count($archivoXML->cabecera->complemento->children()),
                        $archivoXML->cabecera->complemento->count(),
                        !empty($archivoXML->cabecera->complemento)
                    );  --}}

                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: right"><b>Cod. Cliente:</b></td>
                <td>{{ $archivoXML->cabecera->codigoCliente }}</td>
            </tr>
        </tbody>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th><br>CÓDIGO SERVICIO<br><br></th>
                <th>CANTIDAD</th>
                <th><br>UNIDAD DE MEDIDA<br><br></th>
                <th>DESCRIPCIÓN</th>
                <th>PRECIO UNITARIO</th>
                <th>DESCUENTO</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $json = json_encode($archivoXML);
                $array = json_decode($json, true);

                //dd($array);

                //dd($archivoXML);
                //$haber = (array) $archivoXML;
                //$listado_detalles = $haber['detalle'];
                $listado_detalles = $array['detalle'];
                $subTotales = 0;

                //dd($listado_detalles, count($listado_detalles));
                //dd($listado_detalles);

                //dd("pdf" ,$listado_detalles);
            @endphp

            @foreach ($listado_detalles as $d)
                @if (is_array($d))
                    @php
                        $subTotales += (float) $d['subTotal'];
                    @endphp
                    <tr>
                        <td>{{ $d['codigoProducto'] }}</td>
                        <td style="text-align: right">{{ number_format((float) $d['cantidad'],2) }}</td>
                        <td> Unidad (Servicios) </td>
                        <td> {{ $d['descripcion'] }}</td>
                        <td style="text-align: right">
                            {{ number_format((float) $d['precioUnitario'],2) }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format((float) $d['montoDescuento'],2) }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format((float) $d['subTotal'],2) }}
                        </td>
                    </tr>
                @else
                    @php
                        $subTotales += (float) $listado_detalles['subTotal'];
                    @endphp
                    <tr>
                        <td>{{ $listado_detalles['codigoProducto'] }}</td>
                        <td style="text-align: right">{{ number_format((float) $listado_detalles['cantidad'],2) }}</td>
                        <td> Unidad (Servicios) </td>
                        <td> {{ $listado_detalles['descripcion'] }}</td>
                        <td style="text-align: right">
                            {{ number_format((float) $listado_detalles['precioUnitario'],2) }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format((float) $listado_detalles['montoDescuento'],2) }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format((float) $listado_detalles['subTotal'],2) }}
                        </td>
                    </tr>
                    @break
                @endif
            @endforeach

            {{--  @foreach ($listado_detalles  as $d)
                @php
                    $subTotales += (int) $d['subTotal'];
                @endphp
                <tr>
                    <td>{{ $d['codigoProducto'] }}</td>
                    <td style="text-align: right">{{ number_format((int) $d['cantidad'],2) }}</td>
                    <td> Unidad (Servicios) </td>
                    <td> {{ $d['descripcion'] }}</td>
                    <td style="text-align: right">
                        {{ number_format((int) $d['precioUnitario'],2) }}
                    </td>
                    <td style="text-align: right">
                        {{ number_format((int) $d['montoDescuento'],2) }}
                    </td>
                    <td style="text-align: right">
                        {{ number_format((int) $d['subTotal'],2) }}
                    </td>
                </tr>
            @endforeach  --}}
            <tr style="align: right">
                <td  style="background: white; border: none;" colspan="4" rowspan="6">
                    @php
                        //$to = ((int) $archivoXML->cabecera->montoTotal) - ((int) $archivoXML->cabecera->descuentoAdicional);
                        $to = ((float) $archivoXML->cabecera->montoTotal);
                        $number = $to;
                        // Crear una instancia de NumberFormatter para el idioma español
                        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
                        // Convertir el número en su forma literal
                        $literal = $formatter->format($number);

                    @endphp
                    <b>Son: {{ ucfirst($literal) }} 00/100 Bolivianos</b>
                </td>
                <td colspan="2" style="text-align: right; padding-right: 10px;">SUBTOTAL Bs</td>
                {{--  <td  style="text-align: right;"> {{  number_format( (int) $archivoXML->cabecera->montoTotal, 2) }}</td>  --}}
                <td  style="text-align: right;"> {{  number_format( $subTotales, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;">DESCUENTO Bs</td>
                <td style="text-align: right;">
                    {{ number_format( (float) $archivoXML->cabecera->descuentoAdicional, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;">TOTAL Bs</td>
                <td style="text-align: right;">
                    {{--  {{ number_format( (int) $to, 2) }}  --}}
                    {{ number_format( (float) $archivoXML->cabecera->montoTotal, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;">MONTO GIFT CARD Bs</td>
                <td style="text-align: right;">
                    {{ number_format( (float) $archivoXML->cabecera->montoGiftCard, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;"><b>MONTO A PAGAR Bs</b></td>
                {{--  <td style="text-align: right;"><b>{{ number_format($to,2) }}</b></td>  --}}
                <td style="text-align: right;">
                    <b>{{ number_format((float) $archivoXML->cabecera->montoTotal,2) }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 10px;"><b>IMPORTE BASE CRÉDITO FISCAL</b></td>
                <td style="text-align: right;">
                    <b>{{ number_format((float) $archivoXML->cabecera->montoTotal,2) }}</b>
                </td>
                {{--  <td style="text-align: right;"><b>{{ number_format($to,2) }}</b></td>  --}}
            </tr>

            <tr >
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
            </tr>

            <tr style="text-align: center">
                <td colspan="5" style="background: white; border: none">ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY</td>
                <td colspan="2" style="background: white; border: none" rowspan="3">
                    <img src="{{ $rutaImagenQR }}" alt="">
                </td>
            </tr>
            <tr style="text-align: center">
                <td colspan="5" style="background: white; border: none">{{ $archivoXML->cabecera->leyenda }}</td>
            </tr>
            <tr style="text-align: center">
                @if ($factura->tipo_factura === 'online')
                    <td colspan="5" style="background: white; border: none">“Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea”</td>
                @else
                    <td colspan="5" style="background: white; border: none">“Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo”</td>

                @endif
            </tr>
        </tbody>
    </table>

    @if ($factura->estado === "Anulado")
        <p id="anulado">ANULADO</p>
    @endif

</body>

</html>

