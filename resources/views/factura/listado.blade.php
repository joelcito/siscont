@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Adjust Balance-->
    {{-- <div class="modal fade" id="modal_new_empresa" tabindex="-1" aria-hidden="true">
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
                                <input type="text" name="empresa_id" id="empresa_id" value="0">
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
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente" id="codigo_ambiente">
                                    <option></option>
                                    <option value="1">Produccion</option>
                                    <option value="2">Desarrollo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_modalidad" id="codigo_modalidad">
                                    <option></option>
                                    <option value="1">Electronica en Linea</option>
                                    <option value="2">Computarizada en linea</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Codigo de Sistema</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="codigo_sistema" id="codigo_sistema">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Documento Sector</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="documento_sectores" id="documento_sectores">
                                    <option></option>
                                    @foreach ($documentosSectores as $ds)
                                        <option value="2">{{ $ds->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Api Token</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="api_token" id="api_token">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Des. Codigos</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos" id="url_fac_codigos">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Des. Sincronizacion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion" id="url_fac_sincronizacion">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Des. Servicio </label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios" id="url_fac_servicios">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Des. Operaciones</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones" id="url_fac_operaciones">
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Codigos</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos_pro" id="url_fac_codigos_pro">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Sincronizacion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion_pro" id="url_fac_sincronizacion_pro">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Servicio </label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios_pro" id="url_fac_servicios_pro">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Operaciones</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones_pro" id="url_fac_operaciones_pro">
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
    </div> --}}
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Facturas</h1>
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
                    <!--begin::Filter menu-->
                    <div class="m-0">
                        <!--begin::Menu toggle-->
                        <a href="#" class="btn btn-sm btn-flex btn-secondary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-filter fs-6 text-muted me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Filter</a>
                        <!--end::Menu toggle-->
                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_65a1215215a0b">
                            <!--begin::Header-->
                            <div class="px-7 py-5">
                                <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Menu separator-->
                            <div class="separator border-gray-200"></div>
                            <!--end::Menu separator-->
                            <!--begin::Form-->
                            <div class="px-7 py-5">
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fw-semibold">Status:</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <div>
                                        <select class="form-select form-select-solid" multiple="multiple" data-kt-select2="true" data-close-on-select="false" data-placeholder="Select option" data-dropdown-parent="#kt_menu_65a1215215a0b" data-allow-clear="true">
                                            <option></option>
                                            <option value="1">Approved</option>
                                            <option value="2">Pending</option>
                                            <option value="2">In Process</option>
                                            <option value="2">Rejected</option>
                                        </select>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fw-semibold">Member Type:</label>
                                    <!--end::Label-->
                                    <!--begin::Options-->
                                    <div class="d-flex">
                                        <!--begin::Options-->
                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                            <input class="form-check-input" type="checkbox" value="1" />
                                            <span class="form-check-label">Author</span>
                                        </label>
                                        <!--end::Options-->
                                        <!--begin::Options-->
                                        <label class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                                            <span class="form-check-label">Customer</span>
                                        </label>
                                        <!--end::Options-->
                                    </div>
                                    <!--end::Options-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fw-semibold">Notifications:</label>
                                    <!--end::Label-->
                                    <!--begin::Switch-->
                                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
                                        <label class="form-check-label">Enabled</label>
                                    </div>
                                    <!--end::Switch-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Actions-->
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                                    <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Form-->
                        </div>
                        <!--end::Menu 1-->
                    </div>
                    <!--end::Filter menu-->
                    <!--begin::Secondary button-->
                    <!--end::Secondary button-->
                    <!--begin::Primary button-->
                    {{-- <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" >Nueva Empresa</a> --}}
                    {{-- <button class="btn btn-sm fw-bold btn-primary" onclick="modalEmpresa()">Nueva Empresa</button> --}}
                    <!--end::Primary button-->
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
                        <div id="tabla_facturas">

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
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).ready(function() {

            ajaxListado();

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

        function ajaxListado(){
            let datos = {}
            $.ajax({
                    url: "{{ url('factura/ajaxListadoFacturas') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {
                        if(data.estado === 'success'){
                            $('#tabla_facturas').html(data.listado)
                        }else{

                        }
                    }
                })
        }
   </script>
@endsection


