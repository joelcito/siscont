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
                    <h2 class="fw-bold">Formulario de Servicio / Producto</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_servicio">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Documento Sector</label>
                                <select required data-control="select2" name="documento_sector_siat_id_new_servicio" id="documento_sector_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($documentos_sectores_asignados as $dsa)
                                        <option value="{{ $dsa->siat_tipo_documento_sector->id }}">{{ $dsa->siat_tipo_documento_sector->descripcion }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="servicio_producto_id_new_servicio" id="servicio_producto_id_new_servicio">
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 required fw-semibold form-label mb-2">Actividad Economica Siat</label>
                                <select required data-control="select2" name="actividad_economica_siat_id_new_servicio" id="actividad_economica_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($activiadesEconomica as $ae)
                                        <option value="{{ $ae->id }}">{{ $ae->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold required form-label mb-2">Producto Servicio Siat</label>
                                <select required data-control="select2" name="producto_servicio_siat_id_new_servicio" id="producto_servicio_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($productoServicio as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->descripcion_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-6 required fw-semibold form-label mb-2">Unidad Medida Siat</label>
                                <select required data-control="select2" name="unidad_medida_siat_id_new_servicio" id="unidad_medida_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
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


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_servicio_importar_excel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Importacion Servicio / Producto</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_importar_servicios_productos_excel" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Documento Sector</label>
                                <select required data-control="select2" name="documento_sector_siat_id_importar_excel" id="documento_sector_siat_id_importar_excel" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio_importar_excel" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($documentos_sectores_asignados as $dsa)
                                        <option value="{{ $dsa->siat_tipo_documento_sector->id }}">{{ $dsa->siat_tipo_documento_sector->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 required fw-semibold form-label mb-2">Actividad Economica Siat</label>
                                <select required data-control="select2" name="actividad_economica_siat_id_importar_excel" id="actividad_economica_siat_id_importar_excel" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio_importar_excel" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($activiadesEconomica as $ae)
                                        <option value="{{ $ae->id }}">{{ $ae->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold required form-label mb-2">Producto Servicio Siat</label>
                                <select required data-control="select2" name="producto_servicio_siat_id_importar_excel" id="producto_servicio_siat_id_importar_excel" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio_importar_excel" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($productoServicio as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->descripcion_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" onclick="descargarFormatoImportarExcel()" class="btn btn-dark btn-sm btn-icon btn-circle mt-10" title="Descargar Formato"><i class="fa fa-download"></i></button>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold required form-label mb-2">Archivo para Importar</label>
                                <input type="file" class="form-control form-control-sm" id="archivo_excel_importar_excel" name="archivo_excel_importar_excel" required  accept=".xlsx,.xls">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="importarServiciosProductosExcel()">Generar</button>
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
                    <button class="btn btn-sm fw-bold btn-success" onclick="exportarExcel()"><i class="fa fa-file-excel"></i> Exportar Producto / Servicio</button>
                    <button class="btn btn-sm fw-bold btn-danger" onclick="modalImportarServicio()"><i class="fa fa-plus"></i> Importar Producto / Servicio</button>
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

            $("#actividad_economica_siat_id_new_servicio, #producto_servicio_siat_id_new_servicio, #unidad_medida_siat_id_new_servicio, #documento_sectores, #documento_sector_siat_id_new_servicio, #documento_sector_siat_id_importar_excel, #actividad_economica_siat_id_importar_excel, #producto_servicio_siat_id_importar_excel ").select2();

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
            $('#servicio_producto_id_new_servicio').val(0)
            $('#documento_sector_siat_id_new_servicio').val(null).trigger('change')
            $('#actividad_economica_siat_id_new_servicio').val(null).trigger('change')
            $('#producto_servicio_siat_id_new_servicio').val(null).trigger('change')
            $('#unidad_medida_siat_id_new_servicio').val(null).trigger('change')
            $('#numero_serie').val("")
            $('#codigo_imei').val("")
            $('#descrpcion_new_servicio').val("")
            $('#precio_new_servicio').val("")

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
                        }else if(data.estado === 'error'){
                            Swal.fire({
                                icon : 'warning',
                                title: "ALTO!",
                                text : data.text,
                            })
                        }
                        $('#modal_new_servicio').modal('hide');
                    }
                })
            }else{
                $("#formulario_new_servicio")[0].reportValidity();
            }
        }

        function editaraSErvicio(id ,siat_documento_sector_id ,siat_depende_actividades_id ,siat_producto_servicios_id,siat_unidad_medidas_id,numero_serie,codigo_imei ,descripcion, precio){
            $('#servicio_producto_id_new_servicio').val(id)
            $('#documento_sector_siat_id_new_servicio').val(siat_documento_sector_id).trigger('change')
            $('#actividad_economica_siat_id_new_servicio').val(siat_depende_actividades_id).trigger('change')
            $('#producto_servicio_siat_id_new_servicio').val(siat_producto_servicios_id).trigger('change')
            $('#unidad_medida_siat_id_new_servicio').val(siat_unidad_medidas_id).trigger('change')
            $('#numero_serie').val(numero_serie)
            $('#codigo_imei').val(codigo_imei)
            $('#descrpcion_new_servicio').val(descripcion)
            $('#precio_new_servicio').val(precio)
            $('#modal_new_servicio').modal('show')
        }

        function eliminarServicio(id){
            Swal.fire({
                title: "Estas seguro de eliminar el servicio?",
                text: "No podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Eliminar!"
              }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url   : "{{ url('empresa/eliminarServicio') }}",
                        method: "POST",
                        data  : {
                            servicio : id
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

        function exportarExcel(){
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
                url: "{{ url('empresa/exportarServicoProductoExcel') }}",
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
                    link.download = 'servicio_productos.xlsx'; // Nombre del archivo Excel
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

        function modalImportarServicio(){

            $('#documento_sector_siat_id_importar_excel').val(null).trigger('change');
            $('#actividad_economica_siat_id_importar_excel').val(null).trigger('change');
            $('#producto_servicio_siat_id_importar_excel').val(null).trigger('change');
            $('#archivo_excel_importar_excel').val(null);

            $('#modal_new_servicio_importar_excel').modal('show')
        }

        function descargarFormatoImportarExcel(){
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
                url: "{{ url('empresa/descargarFormatoImportarExcel') }}",
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
                    link.download = 'ImportarServiciosProductos.xlsx'; // Nombre del archivo Excel
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

        function importarServiciosProductosExcel(){
            if($("#formulario_importar_servicios_productos_excel")[0].checkValidity()){
                let datos = new FormData($("#formulario_importar_servicios_productos_excel")[0]);
                $.ajax({
                    url   : "{{ url('empresa/importarServiciosProductosExcel') }}",
                    method: "POST",
                    data  : datos,
                    contentType: false,
                    processData: false,
                    success: function (data) {

                        console.log(data);


                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            // ajaxListadoClientes();
                            $('#modal_new_servicio_importar_excel').modal('hide');
                        }else if(data.estado === 'warnig'){

                            // Supongamos que `datosErroneos` es tu array con las observaciones
                            const datosErroneos = data.errores;

                            // Crea un string HTML a partir del array
                            const erroresHtml = datosErroneos.map(error => `<li>${error.texto} en el fila [ ${error.numero} ]</li>`).join('');

                            Swal.fire({
                                icon:'warning',
                                title: "EXITO!",
                                text:  "SE REGISTRO, PERO HAY OBSERVACIONES",
                                html: `
                                    SE REGISTRÓ, PERO HAY OBSERVACIONES:<br>
                                    <ul>${erroresHtml}</ul>
                                `,
                            })

                            $('#modal_new_servicio_importar_excel').modal('hide');

                        }
                    }
                })

                // console.log(datos)
            }else{
                $("#formulario_importar_servicios_productos_excel")[0].reportValidity();
            }
        }
   </script>
@endsection


