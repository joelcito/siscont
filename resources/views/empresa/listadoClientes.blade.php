@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_cliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Cliente</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_cliente_empresa">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombres</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombres_cliente_new_usuaio_empresa" id="nombres_cliente_new_usuaio_empresa" required>
                                <input type="hidden" name="cliente_id_cliente_new_usuaio_empresa" id="cliente_id_cliente_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Ap Paterno</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="ap_paterno_cliente_new_usuaio_empresa" id="ap_paterno_cliente_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="ap_materno_cliente_new_usuaio_empresa" id="ap_materno_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Numero de Celular</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="num_ceular_cliente_new_usuaio_empresa" id="num_ceular_cliente_new_usuaio_empresa">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cedula</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="cedula_cliente_new_usuaio_empresa" id="cedula_cliente_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2">Complemento</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="complemento_cliente_new_usuaio_empresa" id="complemento_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-2">
                                <label class="fs-6 fw-semibold form-label mb-2">Nit</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="nit_cliente_new_usuaio_empresa" id="nit_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Razon Social</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="razon_social_cliente_new_usuaio_empresa" id="razon_social_cliente_new_usuaio_empresa">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2">Correo</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="correo_cliente_new_usuaio_empresa" id="correo_cliente_new_usuaio_empresa">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="guardarClienteEmpresa()">Generar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Clientes</h1>
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
                    <button class="btn btn-success btn-sm" onclick="expoartarExcelClientes()"><i class="fa fa-file-excel"></i>Exportar Excel</button>
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalNuevoCliente()"><i class="fa fa-plus"></i> Nuevo Cliente</button>
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

                        <div id="tabla_clientes">

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
                url: "{{ url('empresa/ajaxListadoClientesEmpresa') }}",
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

        function modalNuevoCliente(){
            $('#cliente_id_cliente_new_usuaio_empresa').val(0)
            $('#nombres_cliente_new_usuaio_empresa').val("")
            $('#ap_paterno_cliente_new_usuaio_empresa').val("")
            $('#ap_materno_cliente_new_usuaio_empresa').val("")
            $('#num_ceular_cliente_new_usuaio_empresa').val("")
            $('#cedula_cliente_new_usuaio_empresa').val("")
            $('#nit_cliente_new_usuaio_empresa').val("")
            $('#razon_social_cliente_new_usuaio_empresa').val("")
            $('#correo_cliente_new_usuaio_empresa').val("")

            $('#modal_new_cliente').modal('show')
        }

        function guardarClienteEmpresa(){
            if($("#formulario_new_cliente_empresa")[0].checkValidity()){
                let datos = $('#formulario_new_cliente_empresa').serializeArray();
                $.ajax({
                    url   : "{{ url('empresa/guardarClienteEmpresaEmpresa') }}",
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
                        }else if(data.estado === 'error'){
                            Swal.fire({
                                icon : 'warning',
                                title: "ALTO!",
                                text : data.text,
                            })
                        }else{

                        }
                        $('#modal_new_cliente').modal('hide');
                    }
                })
            }else{
                $("#formulario_new_cliente_empresa")[0].reportValidity();
            }
        }

        function editarCliente(id,nombres ,ap_paterno,ap_materno ,numero_celular ,nit,razon_social ,cedula ,complemento,correo){

            $('#cliente_id_cliente_new_usuaio_empresa').val(id)
            $('#nombres_cliente_new_usuaio_empresa').val(nombres)
            $('#ap_paterno_cliente_new_usuaio_empresa').val(ap_paterno)
            $('#ap_materno_cliente_new_usuaio_empresa').val(ap_materno)
            $('#complemento_cliente_new_usuaio_empresa').val(complemento)
            $('#num_ceular_cliente_new_usuaio_empresa').val(numero_celular)
            $('#cedula_cliente_new_usuaio_empresa').val(cedula)
            $('#nit_cliente_new_usuaio_empresa').val(nit)
            $('#razon_social_cliente_new_usuaio_empresa').val(razon_social)
            $('#correo_cliente_new_usuaio_empresa').val(correo)

            $('#modal_new_cliente').modal('show')
        }

        function eliminarCliente(id){
            Swal.fire({
                title: "Estas seguro de eliminar al cliente?",
                text: "No podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Eliminar!"
              }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url   : "{{ url('empresa/eliminarClienteEmpresa') }}",
                        method: "POST",
                        data  : {
                            cliente : id
                        },
                        success: function (data) {
                            if(data.estado === 'success'){
                                Swal.fire({
                                    icon:'success',
                                    title: "EXITO!",
                                    text:  data.text,
                                })
                                ajaxListado();
                            }else if(data.estado === 'error'){
                                Swal.fire({
                                    icon : 'warning',
                                    title: "ALTO!",
                                    text : data.text,
                                })
                            }
                        }
                    })
                }
            });
        }

        function expoartarExcelClientes(){
            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando Excel...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            $.ajax({
                url: "{{ url('empresa/expoartarExcelClientes') }}",
                method: "POST",
                // data: datos,
                xhrFields: {
                    responseType: 'blob' // Esto le dice a jQuery que espere un archivo binario (PDF)
                },
                success: function (data, status, xhr) {
                    // // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    Swal.close();

                    // Assume `data` contains the binary response from the server
                    var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Clientes.xlsx'; // Nombre del archivo Excel
                    document.body.appendChild(link); // Required for Firefox
                    link.click();
                    document.body.removeChild(link);
                },
                error: function (xhr, status, error) {
                    // Mostrar error si algo falla
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el EXCEL. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });
        }
   </script>
@endsection


