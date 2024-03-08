@extends('layouts.app')
@section('css')
    {{-- <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" /> --}}
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    {{-- <!--end::Modal - New Card-->
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
                                        @foreach ($motivoAnulacion as $ma)
                                            <option value="{{ $ma->codigo_sin }}">{{ $ma->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="factura_id">
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
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <button class="btn btn-success w-100 mt-4 btn-sm" onclick="buscarEventosSignificativos()" type="button"><i class="fa fa-search"></i>Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-6">
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

     <!--begin::Modal TRAMSERENCIA FACTURA- Add task-->
     <div class="modal fade" id="modalTramsferenciaFactura" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">FORMULARIO DE ANULACION Y TRAMSFERIR FACTURA</h2>
                </div>
                <div class="modal-body scroll-y">

                    <div id="detalle_factura">

                    </div>

                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title ">
                <h1>Listado de Ventas</h1>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="formulario_busqueda_ventas">
                <div class="row">
                    <div class="col-md-1">
                        <label for="">Placa</label>
                        <input type="text" class="form-control" id="buscar_placa" name="buscar_placa">
                    </div>
                    <div class="col-md-1">
                        <label for="">Ap Paterno</label>
                        <input type="text" class="form-control" id="buscar_ap_paterno" name="buscar_ap_paterno">
                    </div>
                    <div class="col-md-1">
                        <label for="">Ap Materno</label>
                        <input type="text" class="form-control" id="buscar_ap_materno" name="buscar_ap_materno">
                    </div>
                    <div class="col-md-1">
                        <label for="">Nombres</label>
                        <input type="text" class="form-control" id="buscar_nombre" name="buscar_nombre">
                    </div>
                    <div class="col-md-2">
                        <label for="">Nit</label>
                        <input type="number" class="form-control" id="buscar_nit" name="buscar_nit">
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha Inicio</label>
                        <input type="date" class="form-control" id="buscar_fecha_ini" name="buscar_fecha_ini">
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha Fin</label>
                        <input type="date" class="form-control" id="buscar_fecha_fin" name="buscar_fecha_fin">
                    </div>
                    <div class="col-md-1">
                        <label for="">Tipo</label>
                        <select name="tipo_emision" id="buscar_tipo_emision" name="buscar_tipo_emision" class="form-control">
                            <option value="">SELECCIONE</option>
                            <option value="Si">FACTURA</option>
                            <option value="No">RECIBO</option>
                        </select>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-success w-100 btn-sm mt-7" onclick="buscarFactura()"><i class="fa fa-search"></i>Buscar</button>
                </div>
            </div>
            <div id="table_roles">

            </div>
        </div>
    </div> --}}

@stop()

@section('js')

@endsection


