@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-700px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario Plan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_plan">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre" id="nombre" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Precio</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="precio" id="precio" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Tipo de Plan</label>
                                <select name="tipo_plan" id="tipo_plan" class="form-control" required>
                                    <option value="Anual">Anual</option>
                                    <option value="Mensual">Mensual</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cantidad de Facturas</label>
                                <input type="number" min="1" class="form-control fw-bold form-control-solid" name="cantidad_factura" id="cantidad_factura" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cantidad de Sucursales</label>
                                <input type="number" min="1" class="form-control fw-bold form-control-solid" name="cantidad_sucursal" id="cantidad_sucursal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cantidad de Punto de Venta</label>
                                <input type="number" min="1" class="form-control fw-bold form-control-solid" name="cantidad_punto_venta" id="cantidad_punto_venta" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cantidad de Usuarios</label>
                                <input type="number" min="1" class="form-control fw-bold form-control-solid" name="cantidad_usuario" id="cantidad_usuario" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="agregarPlan()">Generar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Planes</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalPlan()"><i class="fa fa-plus"></i> Nuevo Plan</button>
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

                        <div id="tabla_planes">

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
                url: "{{ url('plan/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_planes').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function modalPlan(){
            $('#nombre').val("")
            $('#precio').val("")
            $('#tipo_plan').val("")
            $('#cantidad_factura').val("")
            $('#cantidad_sucursal').val("")
            $('#cantidad_punto_venta').val("")
            $('#cantidad_usuario').val("")
            $('#modal_new_plan').modal('show')
        }

        function agregarPlan(){
            if($("#formulario_new_plan")[0].checkValidity()){

                let datos = $('#formulario_new_plan').serializeArray();

                $.ajax({
                    url   : "{{ url('plan/agregarPlan') }}",
                    method: "POST",
                    data  : datos,
                    success: function (data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            ajaxListado();
                            $('#modal_new_plan').modal('hide');
                        }else{
                        }
                    }
                })

            }else{
                $("#formulario_new_plan")[0].reportValidity();
            }
        }

   </script>
@endsection


