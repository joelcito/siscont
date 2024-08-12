@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Formulario de Facturacion Compra y Venta</h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="index.html" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">User Management</li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">Users</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalRol()">Nuevo Rol</button>
                </div> --}}
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-body py-4">

                        <div class="row">
                            <div class="col-md-12 text-center">
                                @if ($verificacionSiat->estado === "success")
                                    <div class="row">
                                        <div class="col-md-6 text-center">
                                            <span class="badge bg-success text-white w-100">{{ $verificacionSiat->resultado->RespuestaComunicacion->mensajesList->descripcion }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            @if ($cuis)
                                                CUIS: {{ $cuis->codigo }}
                                            @else
                                                <span class="badge badge-danger">NO existe un Cuis Vigente para este Usuario</span>
                                            @endif
                                            {{-- CUIS: {{ (session()->has('scuis'))?  session('scuis') : '<span class="text-danger">NO existe la Cuis Vigente</span>'}} --}}
                                        </div>
                                        <div class="col-md-3">
                                            @if ($cufd)
                                                {{-- CUFD: {{ $cufd->codigo_control." ".str_replace("T", " ",substr(session('sfechaVigenciaCufd'), 0 , 16)) }} --}}
                                                CUFD: {{ $cufd->codigo_control." ".$cufd->fecha_vigencia }}
                                            @else
                                                <span class="badge badge-danger">NO existe un Cufd Vigente para este Usuario</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-danger text-white w-100">NO HAY CONECCION CON SIAT</span>
                                @endif
                            </div>
                        </div>

                        <div id="tabla_clientes">

                        </div>

                        <hr>
                        {{--  <div id="tabla_ventas" style="display: none">  --}}
                        <div id="tabla_ventas">
                            <h4 class="text-info text-center">CLIENTE SELECCIONADO</h4>
                            <form id="formulario_cliente_escogido">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Nombre</label>
                                        <input type="text" class="form-control fw-bold form-control-solid" name="nombre_escogido" id="nombre_escogido">
                                        <input type="hidden" name="cliente_id_escogido" id="cliente_id_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap Paterno</label>
                                        <input type="text" class="form-control fw-bold form-control-solid" name="ap_paterno_escogido" id="ap_paterno_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                        <input type="text" class="form-control fw-bold form-control-solid" name="ap_materno_escogido" id="ap_materno_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Cedula</label>
                                        <input type="text" class="form-control fw-bold form-control-solid" name="cedula_escogido" id="cedula_escogido">
                                    </div>
                                </div>
                            </form>
                            <form id="formulario_venta">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="required fw-semibold fs-6 mb-2">Servicio / Producto</label>
                                        <select name="serivicio_id_venta" id="serivicio_id_venta" class="form-control" onchange="identificaSericio(this)" required>
                                            <option value="">SELECCIONE</option>
                                            @foreach ($servicios as $s)
                                            <option value="{{ $s }}">{{ $s->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="required fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                        <input type="text" class="form-control" id="descripcion_adicional" name="descripcion_adicional" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="required fw-semibold fs-6 mb-2">Numero Serie</label>
                                        <input type="number" class="form-control" id="numero_serie" name="numero_serie" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="required fw-semibold fs-6 mb-2">Codigo Imei</label>
                                        <input type="number" class="form-control" id="codigo_imei" name="codigo_imei" min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                        <input type="number" readonly class="form-control" id="precio_venta" name="precio_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_venta" name="cantidad_venta" value="0" min="1" required onkeyup="multiplicarPrecioAlTolta()">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="required fw-semibold fs-6 mb-2">Total</label>
                                        <input type="number" class="form-control" id="total_venta" name="total_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-success btn-sm w-100 mt-9" type="button" onclick="agregarProducto()"><i class="fa fa-plus"></i> Agregar</button>
                                    </div>
                                </div>
                            </form>
                            {{--  <div id="tabla_detalles" style="display: none">  --}}
                            <div id="tabla_detalles">
                                <h3>AQUI OTRO</h3>

                                <table id="carrito" border="1">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se agregarán las filas del carrito -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        var arrayProductos          = [];
        var arrayPagos              = [];

        $(document).ready(function() {

            // ajaxListadoTipoDocumentoSector();
            // ajaxListadoClientes();
            //ajaxListadoServicios();

            $("#serivicio_id_venta").select2();

        });

        function ajaxListadoServicios(){
            let datos = {}
            $.ajax({
                url: "{{ url('factura/ajaxListadoServicios') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_clientes').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function agregarCarrito(){

        }

        function identificaSericio(selected){

            var json = JSON.parse(selected.value);

            $('#precio_venta').val(json.precio)
            $('#cantidad_venta').val(1)
            $('#total_venta').val((1*json.precio))
            $('#numero_serie').val(json.numero_serie)
            $('#codigo_imei').val(json.codigo_imei)

        }

        function agregarProducto(){


            /*

            if($("#formulario_venta")[0].checkValidity()){
                let datoscombi = $('#formulario_venta, #formulario_cliente_escogido').serializeArray();
                $.ajax({
                    url: "{{ url('factura/agregarProducto') }}",
                    type    : 'POST',
                    data    : datoscombi,
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            let cliente = $('#cliente_id_escogido').val();
                            ajaxListadoDetalles(cliente);
                            $('#tabla_detalles').show('toogle')
                        }
                    }
                });
            }else{
                $("#formulario_venta")[0].reportValidity();
            }
            */

        }

        /*

        function escogerCliente(cliente,nombres, ap_paterno, ap_materno, cedula){
            $('#cliente_id_escogido').val(cliente);
            $('#nombre_escogido').val(nombres);
            $('#ap_paterno_escogido').val(ap_paterno);
            $('#ap_materno_escogido').val(ap_materno);
            $('#cedula_escogido').val(cedula);

            $('#tabla_clientes').hide('toogle')
            $('#tabla_ventas').show('toogle')

            ajaxListadoDetalles(cliente);
        }

        function multiplicarPrecioAlTolta(){
            let precio   = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            $('#total_venta').val((precio*cantidad));
            // console.log(precio, cantidad, (precio*cantidad))
        }

        function ajaxListadoDetalles(cliente){
            let datos = {cliente : cliente}
            $.ajax({
                url   : "{{ url('factura/ajaxListadoDetalles') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){

                        if(data.cantidad > 0)
                            $('#tabla_detalles').show('toogle')

                        $('#tabla_detalles').html(data.listado)

                    }else{

                    }
                }
            })
        }

        function descuentoPorItem(detalle, element, total, cliente){

            let ope = total - element.value

            if(ope > 0){
                $.ajax({
                    url   : "{{ url('factura/descuentoPorItem') }}",
                    method: "POST",
                    data  : {
                        detalle : detalle,
                        descunto: element.value
                    },
                    success: function (data) {
                        if(data.estado === 'success'){

                            ajaxListadoDetalles(cliente)
                            $('#bloqueDatosFactura').hide('toogle')

                            // $('#tabla_detalles').html(data.listado)

                            // Swal.fire({
                            //     icon             : 'success',
                            //     title            : data.msg,
                            //     showConfirmButton: false,       // No mostrar botón de confirmación
                            //     timer            : 2000,        // 5 segundos
                            //     timerProgressBar : true
                            // });
                            // ajaxListadoTipoDocumentoSector();

                        }else{

                        }
                    }
                })
            }else{
                Swal.fire({
                    icon             : 'error',
                    title            : 'El descuento no puede exeder al precio del Item',
                    showConfirmButton: false,       // No mostrar botón de confirmación
                    timer            : 2000,        // 5 segundos
                    timerProgressBar : true
                });

                ajaxListadoDetalles(cliente)

            }

        }

        function eliminarDetalle(detalle, cliente){
            $.ajax({
                url   : "{{ url('factura/eliminarDetalle') }}",
                method: "POST",
                data  : {
                    detalle: detalle,
                },
                success: function (data) {
                    if(data.estado === 'success'){

                        ajaxListadoDetalles(cliente)
                        $('#bloqueDatosFactura').hide('toogle')

                    }else{

                    }
                }
            })
        }

        function descuentoAdicionalGlobal(){
            $.ajax({
                url   : "{{ url('factura/descuentoAdicionalGlobal') }}",
                method: "POST",
                data  : {
                    cliente: $('#cliente_id_escogido').val(),
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        let desAdi = $('#descuento_adicional_global').val();
                        let total  = data.valor;
                        let dat = (total) - desAdi
                        if(dat > 0){
                            $('#total_a_pagar_importe').val(dat.toFixed(2))
                            $('#bloqueDatosFactura').hide('toogle')
                        }else{
                            Swal.fire({
                                icon             : 'error',
                                title            : 'El descuento no puede exeder al precio Total',
                                showConfirmButton: false,       // No mostrar botón de confirmación
                                timer            : 2000,        // 5 segundos
                                timerProgressBar : true
                            });

                            $('#descuento_adicional_global').val(0);
                        }

                    }else{

                    }
                }
            })
        }

        function muestraDatosFactura(){
            // $('#bloqueDatosFactura').show('toggle')
            // $('#bloque_tipos_pagos').show('toggle')
            // $('#boton_enviar_factura').show('toggle')
            // $('#boton_enviar_recivo').hide('toggle')
            $.ajax({
                url: "{{ url('factura/arrayCuotasPagar') }}",
                data:{
                    cliente : $('#cliente_id_escogido').val()
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        arrayPagos     = data.detalles;
                        arrayProductos = JSON.parse(data.lista)
                        $('#bloqueDatosFactura').show('toogle')
                    }
                }
            });
        }

        function emitirFactura(){

            let cliente = $('#cliente_id_escogido').val()

            $.ajax({
                url: "{{ url('factura/verificaItemsGeneracion') }}",
                data: {
                    cliente: cliente
                },
                type: 'POST',
                dataType:'json',
                success: function(data) {

                    // console.log(data)

                    if(data.estado === "success"){
                        if(data.cantidad == 0){
                            Swal.fire({
                                icon:   'error',
                                title:  'Error!',
                                text:   "DEBE AL MENOS AGREGAR UN SERVICIO/PRODUCTO",
                                timer: 5000
                            })
                        }else{

                            // if($("#formularioGeneraFactura")[0].checkValidity() && $("#formulario_tipo_pagos")[0].checkValidity()){
                            if($("#formularioGeneraFactura")[0].checkValidity()){


                                // // Obtén el botón y el icono de carga
                                // var boton = $("#boton_enviar_factura");
                                // var iconoCarga = boton.find("i");
                                // // Deshabilita el botón y muestra el icono de carga
                                // boton.attr("disabled", true);
                                // iconoCarga.show();

                                //PONEMOS TODO AL MODELO DEL SIAT EL DETALLE
                                detalle = [];

                                // console.log($('#numero_serie').val());
                                // console.log($('#codigo_imei').val());

                                // console.log("----------------------");
                                // console.log(arrayProductos,arrayProductos[0].numero_serie == null , arrayProductos[0].codigo_imei == null);
                                // console.log("----------------------");

                                arrayProductos.forEach(function (prod){
                                    detalle.push({
                                        actividadEconomica  :   prod.codigo_caeb,
                                        codigoProductoSin   :   prod.codigo_producto,
                                        codigoProducto      :   prod.servicio_id,
                                        descripcion         :   prod.descripcion,
                                        cantidad            :   prod.cantidad,
                                        unidadMedida        :   prod.codigo_clasificador,
                                        precioUnitario      :   prod.precio,
                                        montoDescuento      :   prod.descuento,
                                        subTotal            :   ((prod.cantidad*prod.precio)-prod.descuento),
                                        numeroSerie         :   (prod.numero_serie == null)? null : prod.numero_serie,
                                        numeroImei          :   (prod.codigo_imei == null)? null : prod.codigo_imei
                                    })
                                })

                                console.log(detalle, arrayProductos, arrayPagos)


                                // let numero_factura                  = $('#numero_factura').val();
                                let numero_factura                  = null;
                                // let cuf                             = "123456789";//cambiar
                                // let cufd                            = "{{ session('scufd') }}";  //solo despues de que aga
                                // let direccion                       = "{{ session('sdireccion') }}";//solo despues de que aga
                                let cuf                             = null;//cambiar
                                let cufd                            = null;  //solo despues de que aga
                                let direccion                       = null;//solo despues de que aga
                                var tzoffset                        = ((new Date()).getTimezoneOffset()*60000);
                                let fechaEmision                    = ((new Date(Date.now()-tzoffset)).toISOString()).slice(0,-1);
                                let nombreRazonSocial               = $('#razon_factura').val();
                                let codigoTipoDocumentoIdentidad    = $('#tipo_documento').val()
                                let numeroDocumento                 = $('#nit_factura').val();

                                let complemento;
                                // var complementoValue = $("#complemento").val();
                                // if (complementoValue === null || complementoValue.trim() === ""){
                                //     complemento                     = null;
                                // }else{
                                //     if($('#tipo_documento').val()==5){
                                //         complemento                     = null;
                                //     }else{
                                //         complemento                     = $('#complemento').val();
                                //     }
                                // }

                                let montoTotal                      = $('#total_a_pagar_importe').val();
                                let descuentoAdicional              = $('#descuento_adicional_global').val();
                                let leyenda                         = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
                                let usuario                         = "{{ Auth::user()->name }}";

                                let codigoExcepcion;
                                if ($('#execpcion').is(':checked'))
                                    codigoExcepcion                 = 1;
                                else
                                    codigoExcepcion                 = 0;


                                var factura = [];
                                factura.push({
                                    cabecera: {
                                        // nitEmisor                       :"{{ $empresa->nit }}",
                                        // razonSocialEmisor               :'{{ $empresa->razon_social }}',
                                        // municipio                       :"Santa Cruz",
                                        // telefono                        :"73130500",

                                        nitEmisor                       :null,
                                        razonSocialEmisor               :null,
                                        municipio                       :null,
                                        telefono                        :null,
                                        numeroFactura                   :numero_factura,
                                        cuf                             :cuf,
                                        cufd                            :cufd,
                                        codigoSucursal                  :null,
                                        direccion                       :direccion ,
                                        codigoPuntoVenta                :null,
                                        fechaEmision                    :fechaEmision,
                                        nombreRazonSocial               :nombreRazonSocial,
                                        codigoTipoDocumentoIdentidad    :codigoTipoDocumentoIdentidad,
                                        numeroDocumento                 :numeroDocumento,
                                        complemento                     :null,
                                        // complemento                     :complemento,
                                        codigoCliente                   :numeroDocumento,
                                        codigoMetodoPago                :$('#facturacion_datos_tipo_metodo_pago').val(),
                                        numeroTarjeta                   :null,
                                        montoTotal                      :montoTotal,
                                        montoTotalSujetoIva             :montoTotal,

                                        codigoMoneda                    :$('#facturacion_datos_tipo_metodo_pago').val(),
                                        tipoCambio                      :1,
                                        montoTotalMoneda                :montoTotal,

                                        montoGiftCard                   :null,
                                        descuentoAdicional              :descuentoAdicional,//ver llenado
                                        codigoExcepcion                 :codigoExcepcion,
                                        cafc                            :null,
                                        leyenda                         :leyenda,
                                        usuario                         :usuario,
                                        codigoDocumentoSector           :1
                                    }
                                })

                                detalle.forEach(function (prod1){
                                    factura.push({
                                        detalle:prod1
                                    })
                                })
                                var datos = {factura};
                                var datosCliente = {
                                    'cliente_id': $('#cliente_id_escogido').val(),
                                    'pagos'     : arrayPagos,
                                    // 'empresa'   : "{{ $empresa->id }}"
                                    // 'realizo_pago': $("#realizo_pago").prop("checked"),
                                    'numero_cafc': $('#numero_factura_cafc').val(),
                                    'uso_cafc'                : $('input[name="uso_cafc"]:checked').val(),
                                };
                                var datosRecepcion = {
                                    // 'uso_cafc'                : $('input[name="uso_cafc"]:checked').val(),
                                    // 'codigo_cafc_contingencia': $('#codigo_cafc_contingencia').val()
                                };
                                $.ajax({
                                    url : "{{ url('factura/emitirFactura') }}",
                                    data: {
                                        datos         : datos,
                                        datosCliente  : datosCliente,
                                        // datosRecepcion: datosRecepcion,
                                        modalidad     : $('#tipo_facturacion').val(),
                                        tipo_pago     : $('#tipo_pago').val(),
                                        monto_pagado  : $('#miInput').val(),
                                        cambio        : $('#cambio').val()
                                    },
                                    type: 'POST',
                                    dataType:'json',
                                    success: function(data) {

                                        if(data.estado === "VALIDADA"){
                                            Swal.fire({
                                                icon : 'success',
                                                title: 'Excelente!',
                                                text : 'LA FACTURA FUE VALIDADA',
                                                timer: 3000
                                            })
                                            window.location.href = "{{ url('factura/listado')}}"
                                        }else if(data.estado === "error_email"){
                                            Swal.fire({
                                                icon : 'error',
                                                title: 'Error!',
                                                text : data.msg,
                                            })
                                            // Habilita el botón y oculta el icono de carga después de completar
                                            boton.attr("disabled", false);
                                            iconoCarga.hide();
                                        }else if(data.estado === "OFFLINE"){
                                            Swal.fire({
                                                icon : 'warning',
                                                title: 'Exito!',
                                                text : 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                                                timer: 2000
                                            })
                                            // window.location.href = "{{ url('factura/listado')}}"
                                        }else{
                                            Swal.fire({
                                                icon : 'error',
                                                title: data.msg,
                                                text : 'LA FACTURA FUE RECHAZADA',
                                            })
                                            // Habilita el botón y oculta el icono de carga después de completar
                                            boton.attr("disabled", false);
                                            iconoCarga.hide();
                                        }
                                    }
                                });

                            }else{
                                $("#formularioGeneraFactura")[0].reportValidity();
                                // $("#formulario_tipo_pagos")[0].reportValidity()
                            }
                        }
                    }
                }
            });

        }

        function bloqueCAFC(){
            if($('#tipo_facturacion').val() === "offline"){
                $('#bloque_cafc').show('toggle')
            }else{
                $('#bloque_cafc').hide('toggle')
            }
        }

        function verificaNit(){
            if($('#tipo_documento').val()  === "5"){
                let nit = $('#nit_factura').val();
                $.ajax({
                    url: "{{ url('factura/verificarNit') }}",
                    data: {
                        nit: nit
                    },
                    type: 'POST',
                    dataType:'json',
                    success: function(data) {
                        if(data.estado == "success"){
                            if(data.estadoSiat){
                                $('#execpcion').prop('checked', false);
                                $('#nitsiexiste').show('toggle')
                                $('#nitnoexiste').hide('toggle')
                                // $('#bloque_exepcion').hide('toggle');
                            }else{
                                $('#nitnoexiste').show('toggle')
                                $('#nitsiexiste').hide('toggle')
                                $('#execpcion').prop('checked', true);
                                // $('#bloque_exepcion').show('toggle');
                            }
                        }else{
                            $('#errorValidar').show('toggle')
                        }
                    }
                });
            }else{
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);
                // $('#bloque_exepcion').hide('toggle');
            }
        }
        */

   </script>
@endsection


