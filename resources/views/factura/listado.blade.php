@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton{
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modmodalContingenciaFueraLinea" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">FORMULARIO DE CONTINGENCIA</h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">FECHA</label>
                                    <input type="date" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="fv-row mb-7">
                                    <button class="btn btn-success w-100 mt-4 btn-sm btn-icon" onclick="buscarEventosSignificativos()" type="button"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">EVENTO SIGNIFICATIVO</label>
                                    <select name="evento_significativo_contingencia_select" id="evento_significativo_contingencia_select" class="form-control" onchange="muestraTableFacturaPaquete()">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div id="tablas_facturas_offline" style="display: none">

                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalAnular" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">FORMULARIO DE ANULACION</h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioAnulaciion">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Motivo de anulacion</label>
                                    <select name="codigoMotivoAnulacion" id="codigoMotivoAnulacion" class="form-control" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($siat_motivo_anulaciones as $ma)
                                            <option value="{{ $ma->tipo_clasificador }}">{{ $ma->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="factura_id" name="factura_id">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="anularFactura()" id="boton_anular_factura"> <i class="fa fa-spinner fa-spin" style="display:none;"></i> Anular Factura</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxlg d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Listado de Facturas</h1>
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

                {{-- @dd(Auth::user()->isFacturacionTasaCero()) --}}
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if (Auth::user()->isFacturacionCompraVenta())
                        <a class="btn btn-sm fw-bold btn-primary" href="{{ url('factura/formularioFacturacionCv') }}"><i class="fa fa-plus"></i>Nueva Venta Compra Venta</a>
                    @endif

                    @if (Auth::user()->isFacturacionTasaCero())
                        <a class="btn btn-sm fw-bold btn-primary" href="{{ url('factura/formularioFacturacionTc') }}"><i class="fa fa-plus"></i>Nueva Venta Tasa Cero</a>
                    @endif
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
                        <form id="formulario-busqueda-factura">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">No. Factura</label>
                                    <input type="number" class="form-control form-control-sm" name="buscar_nro_factura" id="buscar_nro_factura">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">C.I. Persona</label>
                                    <input type="number" class="form-control form-control-sm" name="buscar_nro_cedula" id="buscar_nro_cedula">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">NIT</label>
                                    <input type="number" class="form-control form-control-sm" name="buscar_nit" id="buscar_nit">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Inicio</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_inicio" id="buscar_fecha_inicio">
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" name="buscar_fecha_fin" id="buscar_fecha_fin">
                                </div>
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button type="button" id="botom_genera_buscar" class="btn btn-success btn-sm w-100 mt-8 btn-icon" onclick="ajaxListado()"><i class="fa fa-search"></i></button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" id="botom_genera_pdf" class="btn btn-danger btn-sm w-100 btn-icon mt-8" title="Expotar en PDF" onclick="reportePDF()"><i class="fa fa-file-pdf"></i></button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" id="botom_genera_excel" class="btn btn-success btn-sm w-100 btn-icon mt-8" title="Expotar en Excel" onclick="exportarExcel()"><i class="fa fa-file-excel"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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

            // $('#botom_genera_buscar').prop('disabled', true);

            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando Listado...',
                text: 'Por favor espera mientras generamos el listado.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            let datos = $('#formulario-busqueda-factura').serializeArray();
            $.ajax({
                    url: "{{ url('factura/ajaxListadoFacturas') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {
                        if(data.estado === 'success'){
                            $('#tabla_facturas').html(data.listado)
                        }else{

                        }

                        // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                        Swal.close();

                    }
                })
        }

        function modalAnularFactura(factura){
            $('#factura_id').val(factura)
            $('#modalAnular').modal('show')
        }

        function anularFactura(){
            if($("#formularioAnulaciion")[0].checkValidity()){
                let datos = $('#formularioAnulaciion').serializeArray()
                $.ajax({
                    url: "{{ url('factura/anularFactura') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {

                        console.log(data);

                        if(data.estado === 'success'){
                            Swal.fire({
                                icon : 'success',
                                title: "EXITO!",
                                text : "SE ANULO CON EXITO",
                            })
                            ajaxListado();
                            $('#modalAnular').modal('hide')
                        }else if(data.estado === 'error'){
                            Swal.fire({
                                icon : 'error',
                                title: data.descripcion.codigoDescripcion,
                                text : JSON.stringify(data.descripcion.mensajesList),
                                // timer:1500
                            })
                            $('#modalAnular').modal('hide')
                        }
                    }
                })

            }else{
                $("#formularioAnulaciion")[0].reportValidity();
            }
        }

        function modalRecepcionFacuraContingenciaFueraLinea(){
            $('#evento_significativo_contingencia_select').val('')
            $('#tablas_facturas_offline').hide('toggle');
            $('#modmodalContingenciaFueraLinea').modal('show')
        }

        function buscarEventosSignificativos(){
            if($("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo")[0].checkValidity()){
                let datos_formulario = $("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo").serializeArray();
                $.ajax({
                    url: "{{ url('eventosignificativo/buscarEventosSignificativos') }}",
                    method: "POST",
                    data: datos_formulario,
                    success: function (data) {
                        $('#evento_significativo_contingencia_select').empty();
                        if(data.estado === "success"){
                            $('#bloque_no_hay_eventos').hide('toggle');

                            var newOption = $('<option>').text("SELECCIONE").val(null);
                            $('#evento_significativo_contingencia_select').append(newOption);

                            $(data.eventos).each(function(index, element) {
                                var optionText = element.fecha_ini_evento+" | "+element.fecha_fin_evento+" | "+element.descripcion;
                                // var optionValue = element.codigoRecepcionEventoSignificativo;
                                var optionValue = element.id;
                                var newOption = $('<option>').text(optionText).val(optionValue);
                                $('#evento_significativo_contingencia_select').append(newOption);
                            });
                        }else{
                            $('#mensaje_contingencia').text(data.msg)
                            $('#bloque_no_hay_eventos').show('toggle');
                        }
                    }
                })
            }else{
                $("#formularioRecepcionFacuraContingenciaFueraLineaEentoSignificativo")[0].reportValidity();
            }
        }

        function muestraTableFacturaPaquete(){
            let valor = $('#evento_significativo_contingencia_select').val();
            $.ajax({
                url: "{{ url('eventosignificativo/muestraTableFacturaPaquete') }}",
                method: "POST",
                data:{
                    fecha: $('#fecha_contingencia').val(),
                    valor: $('#evento_significativo_contingencia_select').val()
                },
                dataType: 'json',
                success: function (data) {
                    if(data.estado === "success"){
                        $('#tablas_facturas_offline').html(data.listado);
                        $('#tablas_facturas_offline').show('toggle');
                    }else{
                    }
                }
            })
        }

        function mandarFacturasPaquete(){
            let arraye = $('#formularioEnvioPaquete').serializeArray();
            // Agregar un nuevo elemento al array
            arraye.push({ name: 'evento_significativo_id', value: $('#evento_significativo_contingencia_select').val() });
            $.ajax({
                url: "{{ url('eventosignificativo/mandarFacturasPaquete') }}",
                method: "POST",
                data:arraye,
                dataType: 'json',
                success: function (data) {
                    if(data.estado === "success"){
                        ajaxListado();
                        $('#modmodalContingenciaFueraLinea').modal('hide')
                        Swal.fire({
                            icon             : 'success',
                            title            : JSON.stringify(data.msg),
                            showConfirmButton: false,       // No mostrar botón de confirmación
                            // timer            : 2000,        // 5 segundos
                            timerProgressBar : true
                        });
                    }else{
                        Swal.fire({
                            icon             : 'error',
                            title            : JSON.stringify(data.msg),
                            showConfirmButton: false,       // No mostrar botón de confirmación
                            // timer            : 2000,        // 5 segundos
                            timerProgressBar : true
                        });
                    }
                }
            })
        }

        function desanularFacturaAnulado(factura){
            Swal.fire({
                title: "Estas seguro de Revertir la Factura anulada?",
                text: "Esta accion no se podra revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, estoy seguro!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('factura/desanularFacturaAnulado') }}",
                        method: "POST",
                        data:{
                            factura:factura
                        },
                        dataType: 'json',
                        success: function (data) {
                            if(data.estado === "success"){
                                ajaxListado();
                                $('#modmodalContingenciaFueraLinea').modal('hide')
                                Swal.fire({
                                    icon             : 'success',
                                    title            : "EXITO",
                                    text             : JSON.stringify(data.msg),
                                    showConfirmButton: false,                      // No mostrar botón de confirmación
                                    // timer            : 2000,        // 5 segundos
                                    timerProgressBar : true
                                });
                            }else{
                                Swal.fire({
                                    icon             : 'error',
                                    text            : JSON.stringify(data.msg),
                                    title            : "ERROR",
                                    showConfirmButton: false,                      // No mostrar botón de confirmación
                                    // timer            : 2000,        // 5 segundos
                                    timerProgressBar : true
                                });
                            }
                        }
                    })


                    // Swal.fire({
                    // title: "Deleted!",
                    // text: "Your file has been deleted.",
                    // icon: "success"
                    // });
                }
            });
        }

        function reportePDF(){

            let datos = $('#formulario-busqueda-factura').serializeArray();

            // Mostrar SweetAlert2 antes de enviar la solicitud
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera mientras generamos el archivo.',
                allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
                didOpen: () => {
                    Swal.showLoading(); // Mostrar el spinner de carga
                }
            });

            $.ajax({
                url: "{{ url('factura/reportePDF') }}",
                method: "POST",
                data: datos,
                xhrFields: {
                    responseType: 'blob' // Esto le dice a jQuery que espere un archivo binario (PDF)
                },
                success: function (data, status, xhr) {
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    Swal.close();

                    // Crear un enlace temporal para iniciar la descarga
                    var blob = new Blob([data], { type: 'application/pdf' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "reporte_facturas.pdf"; // Nombre del archivo
                    link.click();
                },
                error: function (xhr, status, error) {
                    // Mostrar error si algo falla
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });

        }

        function exportarExcel(){
            let datos = $('#formulario-busqueda-factura').serializeArray();

            // // Mostrar SweetAlert2 antes de enviar la solicitud
            // Swal.fire({
            //     title: 'Generando PDF...',
            //     text: 'Por favor espera mientras generamos el archivo.',
            //     allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
            //     didOpen: () => {
            //         Swal.showLoading(); // Mostrar el spinner de carga
            //     }
            // });

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
                url: "{{ url('factura/reporteExcel') }}",
                method: "POST",
                data: datos,
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
                    link.download = 'reporte_facturas.xlsx'; // Nombre del archivo Excel
                    document.body.appendChild(link); // Required for Firefox
                    link.click();
                    document.body.removeChild(link);
                },
                error: function (xhr, status, error) {
                    // Mostrar error si algo falla
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el PDF. Inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error("Error al generar el PDF: ", error);
                }
            });
        }

   </script>
@endsection


