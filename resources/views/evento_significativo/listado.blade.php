@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_evento_significativo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Evento Significativo</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_evento_significativo">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Tipo de Evento</label>
                                <select data-control="select2" data-placeholder="Seleccione"
                                    data-dropdown-parent="#modal_evento_significativo" data-hide-search="true"
                                    class="form-select form-select-solid fw-bold" name="codigo_tipo_evento"
                                    id="codigo_tipo_evento" required>
                                    <option></option>
                                    @foreach ($siat_evento_significativos as $ses)
                                        <option value="{{ $ses->id }}">{{ $ses->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descripcion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="descripcion"
                                    id="descripcion" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold form-label mb-2 required">Fecha Ini</label>
                                        <input type="date" class="form-control" required id="fecha_inicio"
                                            name="fecha_inicio">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold form-label mb-2 required">Hora (HH:MM:SS):</label>
                                        <input type="time" step="1" class="form-control" required
                                            name="hora_inicio" id="hora_inicio">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold form-label mb-2 required">Fecha Fin</label>
                                        <input type="date" class="form-control" required name="fecha_fin" id="fecha_fin" value="{{ date('d-m-Y') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold form-label mb-2 required">Hora (HH:MM:SS):</label>
                                        <input type="time" step="1" class="form-control" required id="hora_fin"
                                            name="hora_fin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success btn-sm w-100 mt-9" type="button" title="Buscar CUFD"
                                    onclick="buscarCufd()"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Cufd Intervalo</label>
                                <div id="bloque_bloque_cufds">
                                    <select name="cufd_id" id="cufd_id" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm"
                                    onclick="agregarEventoSignificativo()" id="boton_enviar"><i class="fa fa-spinner fa-spin" style="display:none;"></i>Generar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Listado de Eventos Significativos Registrados</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalRol()"><i class="fa fa-plus"></i>Nuevo
                        Evento Significativo</button>
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

                        <div id="tabla_eventos_significativos">

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




            // const inputTime = document.getElementById('time');

            // inputTime.addEventListener('input', function() {
            //     const timeValue = this.value;
            //     const timeParts = timeValue.split(':');

            //     let hour = parseInt(timeParts[0]) || 0;
            //     let minute = parseInt(timeParts[1]) || 0;
            //     let second = parseInt(timeParts[2]) || 0;

            //     // Validación de los valores
            //     hour = Math.min(Math.max(hour, 0), 23);
            //     minute = Math.min(Math.max(minute, 0), 59);
            //     second = Math.min(Math.max(second, 0), 59);

            //     // Formatear los valores para asegurar dos dígitos
            //     hour = String(hour).padStart(2, '0');
            //     minute = String(minute).padStart(2, '0');
            //     second = String(second).padStart(2, '0');

            //     // Reemplazar el valor del input con la hora formateada
            //     this.value = `${hour}:${minute}:${second}`;
            // });

        });

        function ajaxListado() {
            let datos = {}
            $.ajax({
                url: "{{ url('eventosignificativo/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(data) {
                    if (data.estado === 'success') {
                        $('#tabla_eventos_significativos').html(data.listado)
                    } else {

                    }
                }
            })
        }

        function modalRol() {
            $('#descripcion').val('')
            $('#codigo_tipo_evento').val(null).trigger('change');

            var today = new Date().toISOString().split('T')[0];

            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');
            var currentTime = hours + ':' + minutes + ':' + seconds;

            $('#fecha_inicio').val(today)
            $('#hora_inicio').val(currentTime)
            $('#fecha_fin').val(today)
            $('#hora_fin').val(currentTime)

            $('#bloque_bloque_cufds').html('<select name="cufd_id" id="cufd_id" class="form-control" required><option value="">Seleccionar</option></select>');
            $('#modal_evento_significativo').modal('show')
        }

        function agregarEventoSignificativo() {
            if ($("#formulario_new_evento_significativo")[0].checkValidity()) {

                // // Obtén el botón y el icono de carga
                // var boton = $("#boton_enviar");
                // var iconoCarga = boton.find("i");
                // // Deshabilita el botón y muestra el icono de carga
                // boton.attr("disabled", true);
                // iconoCarga.show();

                let datos = $('#formulario_new_evento_significativo').serializeArray();

                // let datos = {}
                $.ajax({
                    url: "{{ url('eventosignificativo/agregarEventoSignificativo') }}",
                    method: "POST",
                    data: datos,
                    success: function(data) {
                        if (data.estado === 'success') {

                            Swal.fire({
                                icon : 'success',
                                title: 'Exito!',
                                text : "Se registro con exito con el codigo "+data.msg,
                                timer: 1500
                            })

                            ajaxListado();
                            $('#modal_evento_significativo').modal('hide')

                            //DESABILITAMOS EL BOTON
                            boton.attr("disabled", false);
                            iconoCarga.hide();
                            //DESABILITAMOS EL BOTON

                        } else {
                            let j = "";
                            if(data.num_error == 1){
                                j = "Error al recuperar el CUFD intente otra vez"
                                //DESABILITAMOS EL BOTON
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            }
                            Swal.fire({
                                title: JSON.stringify(data.msg),
                                text : j,
                                icon : 'error'
                            })
                        }
                    }
                })



            } else {
                $("#formulario_new_evento_significativo")[0].reportValidity();
            }
        }

        function buscarCufd() {
            let fecha_ini = $('#fecha_inicio').val();
            let hora_ini = $('#hora_inicio').val();
            let fecha_fin = $('#fecha_fin').val();
            let hora_fin = $('#hora_fin').val();

            if (fecha_ini != '' && hora_ini != '' && fecha_fin != '' && hora_fin != '') {

                let datos = {
                    fecha_ini: fecha_ini,
                    hora_ini: hora_ini,
                    fecha_fin: fecha_fin,
                    hora_fin: hora_fin
                }

                $.ajax({
                    url: "{{ url('eventosignificativo/buscarCufd') }}",
                    method: "POST",
                    data: datos,
                    success: function(data) {
                        if (data.estado === 'success') {

                            $('#bloque_bloque_cufds').html(data.select)

                            console.log(data.select)

                            // $('#tabla_eventos_significativos').html(data.listado)
                        } else {

                        }
                    }
                })

            } else {
                alert("DEBE LLENAR FECHA INICIO, HORA INICIO, FECHA FINAL Y HORA FINAL")
            }






        }

        // function agregarRol(){
        //     if($("#formulario_new_rol")[0].checkValidity()){

        //         let datos = $('#formulario_new_rol').serializeArray();

        //         $.ajax({
        //             url   : "{{ url('rol/agregarRol') }}",
        //             method: "POST",
        //             data  : datos,
        //             success: function (data) {

        //                 console.log(data)

        //                 if(data.estado === 'success'){
        //                     // console.log(data)
        //                     Swal.fire({
        //                         icon:'success',
        //                         title: "EXITO!",
        //                         text:  "SE REGISTRO CON EXITO",
        //                     })
        //                     ajaxListado();
        //                     $('#modal_new_rol').modal('hide');
        //                     // $('#modal_puntos_ventas').modal('show');
        //                     // $('#tabla_puntos_ventas').html(data.listado)
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
        //         $("#formulario_new_rol")[0].reportValidity();
        //     }
        // }
    </script>
@endsection
