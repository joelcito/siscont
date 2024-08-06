@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_recepcioncompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Recepcion de Compras</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold form-label mb-2 required">Periodo:</label>
                            <select name="periodo" id="periodo">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold form-label mb-2 required">Gestion</label>
                            <input type="number" class="form-control fw-bold form-control-solid" name="gestion" id="gestion" required>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-sm w-100 mt-10" onclick="buscarFacturasRecepcion()"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tabla_facaturas_enviar">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_registrocompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Registro</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_registrocompra">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">NIT Proveedor:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="nit_provedor" id="nit_provedor" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Código de Autorización:</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="codigo_autorizacion" id="codigo_autorizacion" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Número Factura:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="numero_factura" id="numero_factura" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Número DUI/DIM:</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="numero_dui_dim" id="numero_dui_dim" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Fecha Factura/DUI/DIM:</label>
                                <input type="date" class="form-control fw-bold form-control-solid" name="fecha_dui_dim" id="fecha_dui_dim" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe Total Compra:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_total_compra" id="importe_total_compra" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe ICE:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_ice" id="importe_ice" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe IEHD:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_iehd" id="importe_iehd" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe IPJ:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_ipj" id="importe_ipj" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Tasas:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="tasas" id="tasas" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Otro No Sujeto a Crédito Fiscal:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="otro_no_sujeto_credito_fiscal" id="otro_no_sujeto_credito_fiscal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importes Exentos:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importes_exentos" id="importes_exentos" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe Compras Gravadas a Tasa Cero:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_compras_gravadas_tasa_cero" id="importe_compras_gravadas_tasa_cero" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Subtotal:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="subtotal" id="subtotal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descuentos Bonificaciones y Rebajas Sujetas al IVA:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="descuentos_bonificacion_rebajas_sujetas_iva" id="descuentos_bonificacion_rebajas_sujetas_iva" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe Gift Card:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_gif_card" id="importe_gif_card" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Importe Base Crédito Fiscal:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="importe_credito_fiscal" id="importe_credito_fiscal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Crédito Fiscal:</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="credito_fiscal" id="credito_fiscal" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Tipo Compra:</label>
                                <select name="tipo_compra" id="tipo_compra" class="form-control">
                                    <option value="1">INTERNO/ACTIVIDADES GRAVADAS</option>
                                    <option value="2">INTERNO/ACTIVIDADES NO GRAVADAS</option>
                                    <option value="3">SUJETAS A PROPORCIONALIDAD</option>
                                    <option value="4">EXPORTACIONES</option>
                                    <option value="5">INTERNO/EXPORTACIONES</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Código de Control:</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="codigo_control" id="codigo_control" required>
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="agregarRegistroCompra()">Generar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Roles</h1>
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
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-sm fw-bold btn-info" onclick="modalRececpcionCompras()">Recepcion de registro de Compras</button>
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalRol()">Nuevo registro de Compras</button>
                </div>
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

                        <div id="tabla_roles">

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

        $(document).ready(function() {

            // ajaxListadoTipoDocumentoSector();
            ajaxListado();

            $('#tipo_compra').select2()

        });

        function ajaxListado(){
            let datos = {}
            $.ajax({
                url: "{{ url('registrocompras/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_roles').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function modalRol(){
            $('#nombre').val("")
            $('#descripcion').val("")
            $('#modal_new_registrocompra').modal('show')
        }

        function agregarRegistroCompra(){
            if($("#formulario_new_registrocompra")[0].checkValidity()){
                {{--  let datos = $('#formulario_new_registrocompra').serializeArray();  --}}

                var tzoffset                        = ((new Date()).getTimezoneOffset()*60000);
                let fechaEmision                    = ((new Date(Date.now()-tzoffset)).toISOString()).slice(0,-1);

                var datosNew = {
                    nro                : null,
                    nitEmisor          : $('#nit_provedor').val(),
                    razonSocialEmisor  : null,
                    codigoAutorizacion : $('#codigo_autorizacion').val(),
                    numeroFactura      : $('#numero_factura').val(),
                    numeroDuiDim       : $('#numero_dui_dim').val(),
                    fechaEmision       : fechaEmision,
                    montoTotalCompra   : $('#importe_total_compra').val(),
                    importeIce         : $('#importe_ice').val(),
                    importeIehd        : $('#importe_iehd').val(),
                    importeIpj         : $('#importe_ipj').val(),
                    tasas              : $('#tasas').val(),
                    otroNoSujetoCredito: $('#otro_no_sujeto_credito_fiscal').val(),
                    importesExentos    : $('#importes_exentos').val(),
                    importeTasaCero    : $('#importe_compras_gravadas_tasa_cero').val(),
                    subTotal           : $('#subtotal').val(),
                    descuento          : $('#descuentos_bonificacion_rebajas_sujetas_iva').val(),
                    montoGiftCard      : $('#importe_gif_card').val(),
                    montoTotalSujetoIva: $('#importe_credito_fiscal').val(),
                    creditoFiscal      : $('#credito_fiscal').val(),
                    tipoCompra         : $('#tipo_compra').val(),
                    codigoControl      : $('#codigo_control').val(),
                };

                $.ajax({
                    url   : "{{ url('registrocompras/agregarRegistroCompra') }}",
                    method: "POST",
                    data  : datosNew,
                    success: function (data) {
                        console.log(data)

                        if(data.estado === 'success'){
                            // console.log(data)
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            ajaxListado();
                            $('#modal_new_registrocompra').modal('hide');
                            // $('#modal_puntos_ventas').modal('show');
                            // $('#tabla_puntos_ventas').html(data.listado)
                            // location.reload();
                        }else{
                            // console.log(data, data.detalle.mensajesList)
                            // Swal.fire({
                            //     icon:'error',
                            //     title: data.detalle.codigoDescripcion,
                            //     text:  JSON.stringify(data.detalle.mensajesList),
                            //     // timer:1500
                            // })
                        }
                    }
                })

            }else{
                $("#formulario_new_registrocompra")[0].reportValidity();
            }
        }

        function modalRececpcionCompras(){

            $('#modal_new_recepcioncompra').modal('show')

            // let datos = {}
            // $.ajax({
            //     url: "{{ url('registrocompras/ajaxListadoRecepcion') }}",
            //     method: "POST",
            //     data: datos,
            //     success: function (data) {



            //         // if(data.estado === 'success'){
            //         //     $('#tabla_roles').html(data.listado)
            //         // }else{

            //         // }
            //     }
            // })
        }

        function buscarFacturasRecepcion(){
             let datos = {
                periodo:$('#periodo').val(),
                gestion:$('#gestion').val()
             }
            $.ajax({
                url: "{{ url('registrocompras/ajaxListadoRecepcion') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_facaturas_enviar').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function envioPaquetesFacturasCompra(){
            let datos = $('#formulario_facturo_compra_a_enviar').serializeArray()
            $.ajax({
                url: "{{ url('registrocompras/envioPaquetesFacturasCompra') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_facaturas_enviar').html(data.listado)
                    }else{

                    }
                }
            })
        }

        // function ajaxListadoTipoPuntoVenta(){
        //     let datos = {}
        //     $.ajax({
        //         url: "{{ url('sincronizacion/ajaxListadoTipoPuntoVenta') }}",
        //         method: "POST",
        //         data: datos,
        //         success: function (data) {
        //             if(data.estado === 'success'){
        //                 $('#tabla_tipo_punto_venta').html(data.listado)
        //             }else{

        //             }
        //         }
        //     })
        // }


        // function sincronizarTipoDocumentoSector(){
        //     let datos = {
        //         empresa_id : 1
        //     }
        //     $.ajax({
        //         url   : "{{ url('sincronizacion/sincronizarTipoDocumentoSector') }}",
        //         method: "POST",
        //         data  : datos,
        //         success: function (data) {
        //             if(data.estado === 'success'){
        //                 Swal.fire({
        //                     icon             : 'success',
        //                     title            : data.msg,
        //                     showConfirmButton: false,       // No mostrar botón de confirmación
        //                     timer            : 2000,        // 5 segundos
        //                     timerProgressBar : true
        //                 });
        //                 ajaxListadoTipoDocumentoSector();
        //             }else{

        //             }
        //         }
        //     })
        // }

        // function sincronizarTipoPuntoVenta(){
        //     let datos = {
        //         empresa_id : 1
        //     }
        //     $.ajax({
        //         url   : "{{ url('sincronizacion/sincronizarParametricaTipoPuntoVenta') }}",
        //         method: "POST",
        //         data  : datos,
        //         success: function (data) {
        //             if(data.estado === 'success'){
        //                 Swal.fire({
        //                     icon             : 'success',
        //                     title            : data.msg,
        //                     showConfirmButton: false,       // No mostrar botón de confirmación
        //                     timer            : 2000,        // 5 segundos
        //                     timerProgressBar : true
        //                 });
        //                 ajaxListadoTipoPuntoVenta();
        //             }else{

        //             }
        //         }
        //     })
        // }
   </script>
@endsection


