@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_servicio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Servicio</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_servicio">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Actividad Economica Siat</label>
                                <select data-control="select2" name="actividad_economica_siat_id_new_servicio" id="actividad_economica_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($activiadesEconomica as $ae)
                                        <option value="{{ $ae->id }}">{{ $ae->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Producto Servicio Siat</label>
                                <select data-control="select2" name="producto_servicio_siat_id_new_servicio" id="producto_servicio_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($productoServicio as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->descripcion_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Unidad Medida Siat</label>
                                <select data-control="select2" name="unidad_medida_siat_id_new_servicio" id="unidad_medida_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($unidadMedida as $um)
                                        <option value="{{ $um->id }}">{{ $um->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2">Numero de Serie</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="numero_serie" id="numero_serie">
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2">Codigo IMEI</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="codigo_imei" id="codigo_imei">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descripcion del Servicio</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="descrpcion_new_servicio" id="descrpcion_new_servicio" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Precio</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="precio_new_servicio" id="precio_new_servicio" required step="any">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="guardarNewServioEmpresa()">Generar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Producto / Servicio</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalNuevoServicio()"><i class="fa fa-plus"></i> Nuevo Producto / Servicio</button>
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

                        <div id="tabla_producto_servicio">

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

            $("#actividad_economica_siat_id_new_servicio, #producto_servicio_siat_id_new_servicio, #unidad_medida_siat_id_new_servicio, #documento_sectores").select2();

        });

        function ajaxListado(){
            let datos = {}
            $.ajax({
                url: "{{ url('empresa/ajaxListadoProductoServicioEmpresa') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_producto_servicio').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function modalNuevoServicio(){
            $('#servicio_id_new_servicio').val(0)
            $('#modal_new_servicio').modal('show')
        }

        function guardarNewServioEmpresa(){
            if($("#formulario_new_servicio")[0].checkValidity()){
                let datos = $('#formulario_new_servicio').serializeArray();
                $.ajax({
                    url   : "{{ url('empresa/guardarProductoServicioEmpresa') }}",
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
                            $('#modal_new_servicio').modal('hide');
                        }else{

                        }
                    }
                })
            }else{
                $("#formulario_new_servicio")[0].reportValidity();
            }
        }
   </script>
@endsection

