@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_api_servicio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario URL SIAT</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_api_servicio">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Documento Sector</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_api_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold" name="new_api_servicio_documento_sector" id="new_api_servicio_documento_sector" class="form-control">
                                    <option></option>
                                    @foreach ($documentosSectores as $stDocumentoSector)
                                        <option value="{{ $stDocumentoSector->id }}">{{ $stDocumentoSector->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_api_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold" name="new_api_servicio_ambiente" id="new_api_servicio_ambiente" required>
                                    <option></option>
                                    <option value="2">Desarrollo</option>
                                    <option value="1">Produccion</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_api_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold" name="new_api_servicio_modalidad" id="new_api_servicio_modalidad" required>
                                    <option></option>
                                    <option value="1">Electronica en Linea</option>
                                    <option value="2">Computarizada en Linea</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="new_api_servicio_nombre" id="new_api_servicio_nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">URL</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="new_api_servicio_url_servicio" id="new_api_servicio_url_servicio" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="agregarApiServicio()">Generar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Apis Servicios SIAT</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalApiServicio()"><i class="fa fa-plus"></i> Nuevo Api</button>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxlg">
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

            $("#new_api_servicio_documento_sector, #new_api_servicio_ambiente").select2();

        });


        function ajaxListado(){
            let datos = {}
            $.ajax({
                url: "{{ url('urlApiServicoSiat/ajaxListado') }}",
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


        function modalApiServicio(){
            $('#new_api_servicio_nombre').val("")
            $('#new_api_servicio_url_servicio').val("")
            $('#new_api_servicio_documento_sector').val(null).trigger('change');
            $('#new_api_servicio_ambiente').val(null).trigger('change');
            $('#modal_new_api_servicio').modal('show')
        }


        function agregarApiServicio(){
            if($("#formulario_new_api_servicio")[0].checkValidity()){
                let datos = $('#formulario_new_api_servicio').serializeArray();
                $.ajax({
                    url   : "{{ url('urlApiServicoSiat/agregarApiServicio') }}",
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
                            $('#modal_new_api_servicio').modal('hide');
                        }else{
                        }
                    }
                })

            }else{
                $("#formulario_new_api_servicio")[0].reportValidity();
            }
        }

   </script>
@endsection


