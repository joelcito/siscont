@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--end::Modal - New Card-->
    {{-- <div class="modal fade" id="modal_new_rol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario Rol</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_rol">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre" id="nombre" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descripcion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="descripcion" id="descripcion" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="agregarRol()">Generar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div> --}}
    <!--end::Modal - New Card-->

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
                        <div id="tabla_ventas">
                            <h4 class="text-info text-center">CLIENTE</h4>
                            <form id="formulario_cliente_escogido">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Nombre</label>
                                        <input type="text" class="form-control fw-bold form-control-solid" name="nombre_escogido" id="nombre_escogido">
                                        <input type="text" name="cliente_id_escogido" id="cliente_id_escogido">
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
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                        <input type="number" readonly class="form-control" id="precio_venta" name="precio_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad_venta" name="cantidad_venta" value="0" min="1" required onkeyup="multiplicarPrecioAlTolta()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Total</label>
                                        <input type="number" class="form-control" id="total_venta" name="total_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm w-100 mt-9" type="button" onclick="agregarProducto()"><i class="fa fa-plus"></i> Agregar</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h4 class="text-info text-center">DETALLES DE VENTA</h4>
                            <div id="tabla_detalles">

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

        $(document).ready(function() {

            // ajaxListadoTipoDocumentoSector();
            ajaxListadoClientes();

            $("#serivicio_id_venta").select2();

        });

        function ajaxListadoClientes(){
            let datos = {}
            $.ajax({
                url: "{{ url('factura/ajaxListadoClientes') }}",
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

        function escogerCliente(cliente,nombres, ap_paterno, ap_materno, cedula){
            $('#cliente_id_escogido').val(cliente);
            $('#nombre_escogido').val(nombres);
            $('#ap_paterno_escogido').val(ap_paterno);
            $('#ap_materno_escogido').val(ap_materno);
            $('#cedula_escogido').val(cedula);

            ajaxListadoDetalles(cliente);
        }

        function identificaSericio(selected){

            var json = JSON.parse(selected.value);

            $('#precio_venta').val(json.precio)
            $('#cantidad_venta').val(1)
            $('#total_venta').val((1*json.precio))
            // if(json.estado == 'servicio'){
            //     $('.servi').show('toggle');
            //     $('.serviPro').show('toggle');
            //     $("#lavador_id").prop("required", true);
            //     $('#cantidad').removeAttr('max');
            //     $('.serviAlma').hide('toggle');
            // }else{
            //     $.ajax({
            //         url: "{{ url('servicio/cantidadAlmacen') }}",
            //         type: 'POST',
            //         data:{servicio:json.id},
            //         dataType: 'json',
            //         success: function(data) {
            //             if(data.estado === 'success'){
            //                 $('#cantidad_almacen').val(data.cantidaAlacen)
            //                 $('#cantidad').attr('max', data.cantidaAlacen)
            //                 $('.servi').hide('toggle');
            //                 $('.serviPro').show('toggle');
            //                 $("#lavador_id").prop("required", false);
            //                 $('.serviAlma').show('toggle');

            //                 if(json.id == 213 || json.id == 214 || json.id == 215)
            //                     $('#precio').prop('readonly', false);
            //                 else
            //                     $('#precio').prop('readonly', true);

            //                 if(data.cantidaAlacen <= 0)
            //                     $('#btnAgregarProductoChe').prop('disabled', true);
            //                 else
            //                     $('#btnAgregarProductoChe').prop('disabled', false);
            //             }
            //         }
            //     });

            // }

        }


        function multiplicarPrecioAlTolta(){
            let precio   = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            $('#total_venta').val((precio*cantidad));
            // console.log(precio, cantidad, (precio*cantidad))
        }

        function agregarProducto(){
            // let datos = $('#formulario_venta').serializeArray();
            // let datosClie = $('#formulario_cliente_escogido').serializeArray();

            let datoscombi = $('#formulario_venta, #formulario_cliente_escogido').serializeArray();

            // console.log(datos)

            $.ajax({
                url: "{{ url('factura/agregarProducto') }}",
                type    : 'POST',
                // data    : datos,
                data    : datoscombi,
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){

                        let cliente = $('#cliente_id_escogido').val();
                        ajaxListadoDetalles(cliente);

                        // $('#cantidad_almacen').val(data.cantidaAlacen)
                        // $('#cantidad').attr('max', data.cantidaAlacen)
                        // $('.servi').hide('toggle');
                        // $('.serviPro').show('toggle');
                        // $("#lavador_id").prop("required", false);
                        // $('.serviAlma').show('toggle');

                        // if(json.id == 213 || json.id == 214 || json.id == 215)
                        //     $('#precio').prop('readonly', false);
                        // else
                        //     $('#precio').prop('readonly', true);

                        // if(data.cantidaAlacen <= 0)
                        //     $('#btnAgregarProductoChe').prop('disabled', true);
                        // else
                        //     $('#btnAgregarProductoChe').prop('disabled', false);
                    }
                }
            });

        }

        function ajaxListadoDetalles(cliente){
            let datos = {cliente : cliente}
            $.ajax({
                url   : "{{ url('factura/ajaxListadoDetalles') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){

                        $('#tabla_detalles').html(data.listado)

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
      
   </script>
@endsection


