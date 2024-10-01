<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Factura</title>
	<style type="text/css">
		@media print {
		  #btnImprimir {
		    display: none;
		  }
		}

		#botonImpresion {
		    background: #17aa56;
		    color: #fff;
		    border-radius: 7px;
		    /box-shadow: 0 5px #119e4d;/
		    padding: 15px;
		}

		body{
				font-family: Arial, Helvetica, sans-serif;
				}

        .facturaPequena{
            font-size: 9pt;
        }

        .textoCentrado{
            text-align: center;
        }

        .textoCentradoNegrita{
            text-align: center;
            font-weight: 800;
        }

		#fondo{
			/background-image: url("{{ asset('assets/images/factura_szone.jpg') }}");/
			/* width: 892px; */
			/* height: 514px; */
		}

		#tablaProductos{
			font-size: 8pt;
			position: absolute;
			top: 230px;
			left: 0px;
			/* width: 718px; */
		}

		#codigoControlQr{
			font-size: 8pt;
			/* position: relative; */
			/*top: 230px;
			left: 0px;*/
			/* width: 718px; */
		}

        .estatico{
            width: 300px;
            height: 50px;
            word-wrap: break-word;
        }


		/estilos para tablas de datos/
        table.datos {
            /font-size: 13px;/
            /line-height:14px;/
            /* width: 1000; */
            border-collapse: collapse;
            background-color: #fff;
        }
        .datos th {
          height: 10px;
          background-color: #fefefe;
          color: #000;
        }
        .datos td {
          height: 12px;
        }
        .datos th, .datos td {
          border: 2px solid #000;
          padding: 2px;
          text-align: center;
        }
        .datos tr:nth-child(even) {background-color: #f2f2f2;}
		#literalTotal{
			font-size: 8pt;
		}

		#datosEmpresaNit{
			/* font-weight: bold; */
			font-size: 10pt;
			position: absolute;
			top: 0px;
			left: 595px;
			padding: 10px;
			border: 1px solid black;
		}

		#datosEmpresaFactura{
			/* font-weight: bold; */
			font-size: 10pt;
			position: absolute;
			top: 180px;
			left: 0px;
			padding: 5px;
			/border: 1px solid black;/
			width: 891px;
		}

		#txtOriginal{
			font-weight: bold;
			font-size: 12pt;
			position: absolute;
			top: 85px;
			left: 670px;
			width: 150px;
			text-align: center;
		}

		#txtActividad{
			/* font-weight: bold; */
			font-size: 6pt;
			position: absolute;
			top: 110px;
			left: 600px;
			width: 280px;
			text-align: left;
		}

		#txtFactura{
			font-weight: bold;
			font-size: 19pt;
			position: absolute;
			top: 140px;
			left: 350px;
			width: 150px;
			text-align: center;
		}

		#logo{
			position: absolute;
			top: 20px;
			left: 50px;
		}

		#direccionEmpresa{
			font-weight: bold;
			font-size: 6pt;
			position: absolute;
			top: 85px;
			left: 20px;
			width: 220px;
			text-align: center;
		}

        #table_nuew_num_fac{
            /* position: absolute; */
            /* right: 20px;
            top: 20px; */
            font-size: 12px;
            width: 300px;
        }

        #table_detalle{
            width: 300px;
        }

        #btonVolver{
            background-color: black;
            color: white;
            padding: 3px 3px;
            border: 1px solid #ff1500 ;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
        }
        #anulado{
            position: absolute;
            font-size: 75px;
            color:rgb(227, 142, 142, 0.8);
            font-weight: bold;
            top: 35%;
            left: -2%;
            transform: rotate(-45deg);
            /*display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;*/
        }

	</style>
</head>
<body>
    <div style="width: 302px" class="facturaPequena">

        <div class="textoCentrado">
            <b class = "textoCentradoNegrita">
                @if ($empresa->codigo_documento_sector == 8)
                    FACTURA TASA CERO - VENTA DE LIBROS O TRANSPORTE DE CARGA <br> INTERNACIONAL
                @else
                    FACTURA
                @endif
            </b><br>
            <b class = "textoCentradoNegrita">
                @if ($empresa->codigo_documento_sector == 8)
                    SIN DERECHO A CRÉDITO FISCAL
                @else
                    CON DERECHO A CREDITO FISCAL
                @endif
            </b><br>
            {{ $archivoXML->cabecera->razonSocialEmisor }}<br>
            CASA MATRIZ<br>
            No. Punto de Venta {{ $archivoXML->cabecera->codigoPuntoVenta }}<br>
            {{ $archivoXML->cabecera->direccion }}<br>
            Tel. {{ $archivoXML->cabecera->telefono }}<br>
            {{ $archivoXML->cabecera->municipio }}<br>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <b class="textoCentradoNegrita">NIT</b><br>
            {{ $archivoXML->cabecera->nitEmisor }}<br>
            <b class="textoCentradoNegrita">FACTURA N°</b><br>
            {{ $archivoXML->cabecera->numeroFactura }}<br>
            <b class="textoCentradoNegrita">CÓD. AUTORIZACIÓN</b><br>
            <div class="estatico">
                {{ $archivoXML->cabecera->cuf }}
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px"><b>NOMBRE/RAZÓN SOCIAL:</b></td>
                    <td width="150px" style="text-align: left">
                        {{ $archivoXML->cabecera->nombreRazonSocial }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>NIT/CI/CEX:</b></td>
                    <td width="100px" style="text-align: left">
                        {{ $archivoXML->cabecera->numeroDocumento }}
                        @if (!empty($archivoXML->cabecera->complemento))
                            - {{ $archivoXML->cabecera->complemento }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>COD. CLIENTE:</b></td>
                    <td width="100px" style="text-align: left">
                        {{ $archivoXML->cabecera->codigoCliente }}
                        @if (!empty($archivoXML->cabecera->complemento))
                            - {{ $archivoXML->cabecera->complemento }}
                        @endif
                    </td>
                </tr>
                {{-- <tr>
                    <td style="text-align: right; width: 150px"><b>PLACA:</b></td>
                    <td width="100px" style="text-align: left">
                        {{ "placa" }}
                    </td>
                </tr> --}}
                <tr>
                    <td style="text-align: right; width: 150px"><b>FECHA DE EMISION:</b></td>
                    <td width="100px" style="text-align: left">
                        <div>
                            @php
                                $fechaHora = $archivoXML->cabecera->fechaEmision;
                                $dateTime = new DateTime($fechaHora);
                                $formattedDateTime = $dateTime->format('d/m/Y h:i A');
                            @endphp
                            {{ $formattedDateTime }}
                        </div>
                    </td>
                </tr>
            </table>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <b class="textoCentradoNegrita">DETALLE</b><br>

            <div>
                <table id="table_detalle">
                    @php
                        $total                 = 0;
                        $json                  = json_encode($archivoXML);
                        $array                 = json_decode($json, true);
                        $listado_detalles      = $array['detalle'];
                        $subTotales            = 0;
                        $tipo_documento_sector = $empresa->codigo_documento_sector;
                    @endphp
                    @foreach ($listado_detalles as $d)
                        @if (is_array($d))
                            @php
                                $subTotales += (float) $d['subTotal'];
                            @endphp
                            <tr>
                                <td style="text-align: left">
                                    {{--  <b>{{ $d['codigoProducto'] }} - {{ $d['descripcion'] }}</b> <br>  --}}
                                    <b>{{ $d['codigoProducto'] }} - {!! nl2br(e($d['descripcion'])) !!}</b> <br>
                                    Unidad de Medida: Unidad (Servicios) <br>
                                    {{ number_format((float) $d['cantidad'],2) }} X {{ number_format((float) $d['precioUnitario'],2) }} - {{ number_format((float) $d['montoDescuento'],2) }}
                                </td>
                                <td>
                                    <br>
                                    <br>
                                    {{ number_format((float) $d['subTotal'],2) }}
                                </td>
                            </tr>
                        @else
                            @php
                                $subTotales += (float) $listado_detalles['subTotal'];
                            @endphp
                            <tr>
                                <td>
                                    {{--  {{ $listado_detalles['codigoProducto'] }} - {{ $listado_detalles['descripcion'] }} <br>  --}}
                                    {{ $listado_detalles['codigoProducto'] }} - {!! nl2br(e($listado_detalles['descripcion'])) !!} <br>
                                    Unidad de Medida: Unidad (Servicios) <br>
                                    {{ number_format((float) $listado_detalles['cantidad'],2) }} X {{ number_format((float) $listado_detalles['precioUnitario'],2) }} - {{ number_format((float) $listado_detalles['montoDescuento'],2) }}
                                </td>
                                <td>
                                    <br>
                                    <br>
                                    {{ number_format((float) $listado_detalles['subTotal'],2) }}
                                </td>
                            </tr>
                            @break
                        @endif
                    @endforeach
                </table>
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px">SUBTOTAL Bs</td>
                    <td width="150px" style="text-align: right">
                        {{  number_format( $subTotales, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">DESCUENTO Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $archivoXML->cabecera->descuentoAdicional, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">TOTAL Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $archivoXML->cabecera->montoTotal, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">MONTO GIFT CARD Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $archivoXML->cabecera->montoGiftCard, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>MONTO A PAGAR Bs</b></td>
                    <td width="100px" style="text-align: right">
                        <b>{{ number_format((float) $archivoXML->cabecera->montoTotal,2) }}</b>
                    </td>
                </tr>
                @if ($tipo_documento_sector == 1)
                    <tr>
                        <td style="text-align: right; width: 150px"><b>IMPORTE BASE CRÉDITO FISCAL Bs</b></td>
                        <td width="100px" style="text-align: right">
                            <b>{{ number_format((float) $archivoXML->cabecera->montoTotal,2) }}</b>
                        </td>
                    </tr>
                @endif
            </table>
            <br>
            <div style="text-align: left">
                {{-- ANTIGUO --}}
                {{-- @php
                    $to        = ((float) $archivoXML->cabecera->montoTotal);
                    $number    = $to;
                    $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
                    $literal   = $formatter->format($number);
                @endphp
                <b>Son: {{ ucfirst($literal) }} 00/100 Bolivianos</b> --}}
                @php
                    $to = (float) $archivoXML->cabecera->montoTotal;

                    // Separar la parte entera y la parte decimal del monto
                    $entero = floor($to); // Parte entera
                    $decimal = round(($to - $entero) * 100); // Parte decimal, redondeada a dos decimales

                    // Crear una instancia de NumberFormatter para el idioma español
                    $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);

                    // Convertir solo la parte entera a su forma literal
                    $literal = $formatter->format($entero);
                @endphp
                <b>Son: {{ ucfirst($literal) }} {{ sprintf('%02d', $decimal) }}/100 Bolivianos</b>
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <p style="font-size:11px">
                ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY
            </p>
            <br>
            <p style="font-size:11px">
                Ley N&deg; 453: El proveedor debera suministrar el servicio en las modalidades y terminos ofertados o convenidos.
            </p>
            @if ($factura->tipo_factura === 'online')
                “Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea”
            @else
                “Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido fuera de línea, verifique su envío con su proveedor o en la página web www.impuestos.gob.bo”
            @endif
            <br>
            <br>
            <center>
                <div id="qrcode"></div>
            </center>
            <br>
            @if ($factura->estado === "Anulado")
                <p id="anulado">ANULADO</p>
            @endif


        <div>
            <center>
                <div id="btnImprimir">
                    <input type="button" id="botonImpresion" value="IMPRIMIR" onClick="window.print()">
                    <a id="btonVolver" href="{{ URL::previous() }}" class="btn btn-primary btn-dark">VOLVER</a>
                </div>
            </center>
        </div>

    </div>
	<script src="{{ asset('assets/js/qrcodejs/qrcode.js') }}"></script>
	<script>
        @php
            $cuf           = (string)$cabeza['cabecera']->cuf;
            $numeroFactura = (string)$cabeza['cabecera']->numeroFactura;
            $qu_url        = $empresa->url_verifica;
            $nit           = $empresa->nit;
        @endphp
        let cadenaQr = '{{ $qu_url }}?nit={{ $nit }}&cuf={{ $cuf }}&numero={{ $numeroFactura }}&t=1';
        var options = {
            text: cadenaQr,
            width: 120,
            height: 120,
            colorDark: "#000000",   // Color de los módulos oscuros
            colorLight: "#ffffff"   // Color de los módulos claros
        };
        var qr = new QRCode(document.getElementById("qrcode"), options);

	</script>

</body>
</html>
