@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_rol" tabindex="-1" aria-hidden="true">
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
                    {{--  <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
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
                    </ul>  --}}
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalRol()"><i class="fa fa-plus"></i> Nuevo Rol</button>
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

        });

        function ajaxListado(){
            let datos = {}
            $.ajax({
                url: "{{ url('rol/ajaxListado') }}",
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
            $('#modal_new_rol').modal('show')
        }

        function agregarRol(){
            if($("#formulario_new_rol")[0].checkValidity()){

                let datos = $('#formulario_new_rol').serializeArray();

                $.ajax({
                    url   : "{{ url('rol/agregarRol') }}",
                    method: "POST",
                    data  : datos,
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
                            $('#modal_new_rol').modal('hide');
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
                $("#formulario_new_rol")[0].reportValidity();
            }
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


