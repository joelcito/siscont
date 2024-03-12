@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Adjust Balance-->
    <div class="modal fade" id="modal_new_empresa" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-850px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario Empresa</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <!--begin::Form-->
                    <form id="formulario_empresa" class="form" action="#">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Nombre Empresa</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre_empresa" id="nombre_empresa">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Nit Empresa</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nit_empresa" id="nit_empresa">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Razon Social</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="razon_social" id="razon_social">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente" id="codigo_ambiente">
                                    <option></option>
                                    <option value="2">Desarrollo</option>
                                    <option value="1">Produccion</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Codigo de Sistema</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="codigo_sistema" id="codigo_sistema">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Documento Sector</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="documento_sectores" id="documento_sectores">
                                    <option></option>
                                    {{-- @foreach ($documentosSectores as $ds)
                                        <option value="2">{{ $ds->descripcion }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Fac. Codigos</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos" id="url_fac_codigos">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Fac. Sincronizacion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion" id="url_fac_sincronizacion">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Fac. Servicio </label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios" id="url_fac_servicios">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Fac. Operaciones</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones" id="url_fac_operaciones">
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="guardarEmpresa()">Guardar </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <!--begin::Content wrapper-->
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
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalEmpresa()">Nueva Empresa</button>
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
                                                <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#kt_contact_view_meetings">
                                                <i class="ki-duotone ki-calendar-8 fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                    <span class="path6"></span>
                                                </i>Meetings</a>
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
                                            <div class="tab-pane fade" id="kt_contact_view_general" role="tabpanel">
                                                <h1>PRIMERO</h1>
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

            ajaxListadoTipoDocumentoSector()

            // ajaxListado();

            // $('#kt_table_users').DataTable({
            //     lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            //     dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            //     language: {
            //     paginate: {
            //         first : 'Primero',
            //         last : 'Último',
            //         next : 'Siguiente',
            //         previous: 'Anterior'
            //     },
            //     search : 'Buscar:',
            //     lengthMenu: 'Mostrar _MENU_ registros por página',
            //     info : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            //     emptyTable: 'No hay datos disponibles'
            //     },
            //     order:[],
            //     //  searching: true,
            //     responsive: true
            // });


        });

        // function modalEmpresa(){
        //     $('#modal_new_empresa').modal('show')
        // }

        // function guardarEmpresa(){
        //     if($("#formulario_empresa")[0].checkValidity()){
        //         console.log($('#formulario_empresa').serializeArray())

        //         let datos = $('#formulario_empresa').serializeArray()

        //         $.ajax({
        //             url: "{{ url('empresa/guarda') }}",
        //             method: "POST",
        //             data: datos,
        //             success: function (data) {
        //                 if(data.estado === 'success'){
        //                     // console.log(data)
        //                     Swal.fire({
        //                         icon:'success',
        //                         title: "EXITO!",
        //                         text:  "SE REGISTRO CON EXITO",
        //                     })
        //                     ajaxListado();
        //                     $('#modal_new_empresa').modal('hide')
        //                     // location.reload();
        //                 }else{
        //                     // console.log(data, data.detalle.mensajesList)
        //                     // Swal.fire({
        //                     //     icon:'error',
        //                     //     title: data.detalle.codigoDescripcion,
        //                     //     text:  JSON.stringify(data.detalle.mensajesList),
        //                     //     // timer:1500
        //                     // })
        //                 }
        //             }
        //         })

        //     }else{
        //         $("#formularioTramsfereciaFactura")[0].reportValidity();
        //     }
        // }

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

        function sincronizarTipoDocumentoSector(){
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
   </script>
@endsection


