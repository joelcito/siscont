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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Sincronizacion de Catalogos</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalEmpresa()">Nueva Empresa</button>
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
                            <div class="col-md-12">

                                <div class="card card-flush h-lg-100" id="kt_contacts_main">
                                    <div class="card-body pt-5">
                                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x fs-6 fw-semibold mt-6 mb-8 gap-2">
                                            <!--begin:::Tab item-->
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4 active" data-bs-toggle="tab" href="#sincro_doc_sector">
                                                <i class="ki-duotone ki-home fs-4 me-1"></i>Doc Sector</a>
                                            </li>

                                            <!--end:::Tab item-->
                                            <!--begin:::Tab item-->
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#sincro_tipo_punto_venta">
                                                <i class="ki-duotone ki-calendar-8 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                    <span class="path6"></span>
                                                </i>Tip. Pun. Venta</a>
                                            </li>
                                            <!--end:::Tab item-->
                                            <!--begin:::Tab item-->
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_activity">
                                                <i class="ki-duotone ki-save-2 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Activity</a>
                                            </li>
                                            <!--end:::Tab item-->
                                        </ul>
                                        <!--end:::Tabs-->
                                        <!--begin::Tab content-->
                                        <div class="tab-content" id="">

                                            <!--begin:::Tab pane-->
                                            <div class="tab-pane fade show active" id="sincro_doc_sector" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="tabla_tipo_documento_sectores">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane-->
                                            <!--begin:::Tab pane-->
                                            <div class="tab-pane fade" id="sincro_tipo_punto_venta" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="tabla_tipo_punto_venta">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane-->
                                            <!--begin:::Tab pane-->
                                            <div class="tab-pane fade" id="kt_contact_view_meetings" role="tabpanel">
                                                <h1>SEGUNDO</h1>
                                            </div>
                                            <!--end:::Tab pane-->
                                            <!--begin:::Tab pane-->
                                            <div class="tab-pane fade" id="kt_contact_view_activity" role="tabpanel">
                                                <h1>TERCERO</h1>
                                            </div>
                                            <!--end:::Tab pane-->
                                        </div>
                                        <!--end::Tab content-->
                                    </div>
                                    <!--end::Card body-->
                                </div>

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

            ajaxListadoTipoDocumentoSector();
            ajaxListadoTipoPuntoVenta();

        });

        function ajaxListadoTipoDocumentoSector(){
            let datos = {}
            $.ajax({
                url: "{{ url('sincronizacion/ajaxListadoTipoDocumentoSector') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_tipo_documento_sectores').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function ajaxListadoTipoPuntoVenta(){
            let datos = {}
            $.ajax({
                url: "{{ url('sincronizacion/ajaxListadoTipoPuntoVenta') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_tipo_punto_venta').html(data.listado)
                    }else{

                    }
                }
            })
        }


        function sincronizarTipoDocumentoSector(){
            let datos = {
                empresa_id : 1
            }
            $.ajax({
                url   : "{{ url('sincronizacion/sincronizarTipoDocumentoSector') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_tipo_documento_sectores').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function sincronizarTipoPuntoVenta(){
            let datos = {
                empresa_id : 1
            }
            $.ajax({
                url   : "{{ url('sincronizacion/sincronizarParametricaTipoPuntoVenta') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_tipo_punto_venta').html(data.listado)
                    }else{

                    }
                }
            })
        }
   </script>
@endsection

