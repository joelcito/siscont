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
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario Empresa</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y mx-5 mx-xlg-15 my-7">
                    <!--begin::Form-->
                    <form id="formulario_empresa" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre Empresa</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="nombre_empresa" id="nombre_empresa" required>
                                <input type="hidden" name="empresa_id" id="empresa_id" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nit Empresa</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="nit_empresa" id="nit_empresa" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Razon Social</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="razon_social" id="razon_social" required>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Municipio</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="municipio" id="municipio" required>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Celular</label>
                                <input type="number" class="form-control fw-bold form-control-solid form-control-sm" name="celular" id="celular" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente" id="codigo_ambiente" required>
                                    <option></option>
                                    <option value="1">Produccion</option>
                                    <option value="2">Desarrollo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_modalidad" id="codigo_modalidad" required>
                                    <option></option>
                                    <option value="1">Electronica en Linea</option>
                                    <option value="2">Computarizada en linea</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Codigo de Sistema</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="codigo_sistema" id="codigo_sistema" required>
                            </div>
                            {{-- <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Documento Sector</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="documento_sectores" id="documento_sectores">
                                    <option></option>
                                    @foreach ($documentosSectores as $ds)
                                        <option value="2">{{ $ds->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Codigo de CAFC</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="codigo_cafc" id="codigo_cafc">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Api Token</label>
                                <input type="text" class="form-control fw-bold form-control-solid form-control-sm" name="api_token" id="api_token" required>
                            </div>
                        </div>
                        {{-- <div class="row mt-5">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Codigos</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos" id="url_fac_codigos">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Sincronizacion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion" id="url_fac_sincronizacion">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Servicio </label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios" id="url_fac_servicios">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Url Operaciones</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones" id="url_fac_operaciones">
                            </div>
                        </div> --}}

                        <div class="row mt-5">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Logo</label>
                                <input type="file" class="form-control fw-bold form-control-solid form-control-sm" name="logo_empresa" id="logo_empresa" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Archivo .P12</label>
                                <input type="file" class="form-control fw-bold form-control-solid form-control-sm" name="fila_archivo_p12" id="fila_archivo_p12">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Contraseña archivo .P12</label>
                                <input type="password" class="form-control fw-bold form-control-solid form-control-sm" name="contrasenia_archivo_p12" id="contrasenia_archivo_p12">
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-sm w-100" onclick="guardarEmpresa()">Guardar </button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Empresas</h1>
                    <!--end::Title-->
                    {{--  <!--begin::Breadcrumb-->
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
                    <!--end::Breadcrumb-->  --}}
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalEmpresa()"><i class="fa fa-plus"></i> Nueva Empresa</button>
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
                    <!--begin::Card header-->
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <!--begin::Table-->
                        <div id="tabla_empresas">

                        </div>
                        <!--end::Table-->
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

        function modalEmpresa(){
            $('#modal_new_empresa').modal('show')
        }

        function guardarEmpresa(){
            if($("#formulario_empresa")[0].checkValidity()){
                // let datos = $('#formulario_empresa').serializeArray()
                let datos = new FormData($("#formulario_empresa")[0]);


                $.ajax({
                    url: "{{ url('empresa/guarda') }}",
                    method: "POST",
                    data: datos,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if(data.estado === 'success'){
                            // console.log(data)
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            ajaxListado();
                            $('#modal_new_empresa').modal('hide')
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
                $("#formulario_empresa")[0].reportValidity();
            }
        }

        function ajaxListado(){
            let datos = {}
            $.ajax({
                url: "{{ url('empresa/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_empresas').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function eliminarEmpresa(empresa){
            Swal.fire({
                title: "Estas seguro de eliminar la empresa?",
                text: "No podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Eliminar!"
              }).then((result) => {
                if (result.isConfirmed) {

                    let datos = {empresa:empresa}
                    $.ajax({
                        url: "{{ url('empresa/eliminarEmpresa') }}",
                        method: "POST",
                        data: datos,
                        success: function (data) {
                            if(data.estado === 'success'){
                                Swal.fire({
                                    icon:'success',
                                    title: "EXITO!",
                                    text:  "SE ELIMINO CON EXITO",
                                })

                                ajaxListado()
                            }else{

                            }
                        }
                    })

                }
            });
        }
   </script>
@endsection


