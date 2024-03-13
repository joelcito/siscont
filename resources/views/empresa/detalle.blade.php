@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .select-modal{
            z-index: 1050; /* Ajusta según sea necesario, debe ser mayor que el z-index del modal */
            position: relative; /* O absolute según sea necesario */
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_genera_cuis" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario Generar Cuis</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_genera_cuis">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_genera_cuis" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente_cuis" id="codigo_ambiente_cuis" disabled>
                                    <option></option>
                                    <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                    <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                </select>
                                <input type="text" name="codigo_punto_venta_id_cuis" id="codigo_punto_venta_id_cuis">
                                <input type="text" name="codigo_sucursal_id_cuis" id="codigo_sucursal_id_cuis">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_genera_cuis" data-hide-search="true" class="form-select form-select-solid fw-bold" name="modalidad_cuis" id="modalidad_cuis" disabled>
                                    <option></option>
                                    <option value="1" {{ ($empresa->codigo_modalidad == 1)? 'selected' : '' }}>Electronica en Linea</option>
                                    <option value="2" {{ ($empresa->codigo_modalidad == 2)? 'selected' : '' }}>Computarizada en linea</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="generarCuis()">Generar</button>
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

    <!--begin::Modal - Adjust Balance-->
    <div class="modal fade" id="modal_new_sucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-400px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Sucursal</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                {{-- <div class="modal-body scroll-y mx-5 mx-xl-15 my-7"> --}}
                <div class="modal-body scroll-y">
                    <form id="formulario_sucursal">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre_sucursal" id="nombre_sucursal" required>
                                <input type="hidden" name="empresa_id_sucursal" id="empresa_id_sucursal" value="{{ $empresa->id }}" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Codigo Sucursal</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="codigo_sucursal" id="codigo_sucursal" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Direccion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="direccion_sucursal" id="direccion_sucursal" required>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-success w-100" onclick="guardarSucursal()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <!--begin::Modal - Adjust Balance-->
    <div class="modal fade" id="modal_puntos_ventas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Puntos de ventas del sucursal <span class="text-info" id="name_sucursal"></span></h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                {{-- <div class="modal-body scroll-y mx-5 mx-xl-15 my-7"> --}}
                <div class="modal-body scroll-y">
                    <div id="tabla_puntos_ventas">

                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <!--begin::Modal - Adjust Balance-->
    <div class="modal fade" id="modal_new_punto_venta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-400px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Punto de Venta</h2>
                    {{-- <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div> --}}
                </div>
                {{-- <div class="modal-body scroll-y mx-5 mx-xl-15 my-7"> --}}
                <div class="modal-body scroll-y">
                    <form id="formulario_sucursal">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente_punto_venta" id="codigo_ambiente_punto_venta">
                                    <option></option>
                                    <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                    <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre_sucursal" id="nombre_sucursal" required>
                                <input type="text" name="sucursal_id_punto_venta" id="sucursal_id_punto_venta" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Codigo Sucursal</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="codigo_sucursal" id="codigo_sucursal" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Direccion</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="direccion_sucursal" id="direccion_sucursal" required>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <button class="btn btn-sm btn-success w-100" onclick="guardarSucursal()">Guardar</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-sm btn-dark w-100" onclick="cancelarCreacionPuntoVenta()">Cancelar</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Detalle de la Empresa <span class="text-info">Name Empresa</span></h1>
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
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="formulario_empresa">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="fs-6 fw-semibold form-label mb-2">Nombre Empresa</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="nombre_empresa" id="nombre_empresa" value="{{ $empresa->nombre }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fs-6 fw-semibold form-label mb-2">Nit Empresa</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="nit_empresa" id="nit_empresa" value="{{ $empresa->nit }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fs-6 fw-semibold form-label mb-2">Razon Social</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="razon_social" id="razon_social" value="{{ $empresa->razon_social }}">
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                            <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente" id="codigo_ambiente">
                                                <option></option>
                                                <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                                <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Modalidad</label>
                                            <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_modalidad   " id="codigo_modalidad ">
                                                <option></option>
                                                <option value="1" {{ ($empresa->codigo_modalidad == 1)? 'selected' : '' }}>Electronica en Linea</option>
                                                <option value="2" {{ ($empresa->codigo_modalidad == 2)? 'selected' : '' }}>Computarizada en linea</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Codigo de Sistema</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="codigo_sistema" id="codigo_sistema" value="{{ $empresa->codigo_sistema }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Documento Sector</label>
                                            <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="documento_sectores" id="documento_sectores">
                                                <option></option>
                                                <option value="1">NEVO</option>
                                                @foreach ($documentosSectores as $ds)
                                                    <option value="2" {{ ($ds->descripcion == "2")? "selected" : "" }}>{{ $ds->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <label class="fs-6 fw-semibold form-label mb-2">Api Token</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="api_token" id="api_token" value="{{ $empresa->api_token }}">
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Des. Codigos</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos" id="url_fac_codigos" value="{{ $empresa->url_facturacionCodigos }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Des. Sincronizacion</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion" id="url_fac_sincronizacion" value="{{ $empresa->url_facturacionSincronizacion }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Des. Servicio </label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios" id="url_fac_servicios" value="{{ $empresa->url_servicio_facturacion_compra_venta }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Des. Operaciones</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones" id="url_fac_operaciones" value="{{ $empresa->url_facturacion_operaciones }}">
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Codigos</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_codigos_pro" id="url_fac_codigos_pro" value="{{ $empresa->url_facturacionCodigos_pro }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Sincronizacion</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_sincronizacion_pro" id="url_fac_sincronizacion_pro" value="{{ $empresa->url_facturacionSincronizacion_pro }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Servicio </label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_servicios_pro" id="url_fac_servicios_pro" value="{{ $empresa->url_servicio_facturacion_compra_venta_pro }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-6 fw-semibold form-label mb-2">Url Pro. Operaciones</label>
                                            <input type="text" class="form-control fw-bold form-control-solid" name="url_fac_operaciones_pro" id="url_fac_operaciones_pro" value="{{ $empresa->url_facturacion_operaciones_pro }}">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button class="btn btn-success w-100 btn-sm" onclick="guardarEmpresa()">Guardar </button>
                            </div>
                        </div>
                        <hr>
                        <div class="card card-flush h-lg-100" id="kt_contacts_main">
                            <div class="card-body pt-5">
                                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x fs-6 fw-semibold mt-6 mb-8 gap-2">
                                    <!--begin:::Tab item-->
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary d-flex align-items-center pb-4 active" data-bs-toggle="tab" href="#sincro_doc_sector">
                                        <i class="ki-duotone ki-home fs-4 me-1"></i>Sucursales</a>
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
                                                <div id="tabla_sucursales">

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
        </div>
    </div>
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

            ajaxListadoSucursal();
            // $('#codigo_ambiente_punto_venta').selectpicker();
            // $('#codigo_ambiente_punto_venta').select2({
            //     placeholder: 'Seleccione',
            //     minimumResultsForSearch: -1 // Para ocultar la barra de búsqueda
            // });

            $('#codigo_ambiente_punto_venta').select2();

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

        function ajaxListadoSucursal(){
            let datos = {}
            $.ajax({
                url: "{{ url('empresa/ajaxListadoSucursal') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_sucursales').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function modalNuevoSucursal(){
            $('#modal_new_sucursal').modal('show')
        }

        function guardarSucursal(){
            if($("#formulario_sucursal")[0].checkValidity()){

                let datos = $('#formulario_sucursal').serializeArray();

                $.ajax({
                    url   : "{{ url('empresa/guardaSucursal') }}",
                    method: "POST",
                    data  : datos,
                    success: function (data) {

                        console.log(data)

                        if(data.estado === 'success'){
                            // console.log(data)
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            ajaxListadoSucursal();
                            $('#modal_new_sucursal').modal('hide')
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
                $("#formulario_sucursal")[0].reportValidity();
            }
        }

        function modalPuntoVentas(sucursal, nombre, codigo_sucursal){
            let datos = {
                sucursal:sucursal
            }
            $.ajax({
                url: "{{ url('empresa/ajaxListadoPuntoVenta') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        // console.log(data)
                        // Swal.fire({
                        //     icon:'success',
                        //     title: "EXITO!",
                        //     text:  "SE REGISTRO CON EXITO",
                        // })
                        // ajaxListado();
                        $('#tabla_puntos_ventas').html(data.listado)
                        $('#modal_puntos_ventas').modal('show');
                        $('#name_sucursal').text(nombre)
                        $('#sucursal_id_punto_venta').val(codigo_sucursal)
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
        }

        function modalNuevoPuntoVenta(){
            $('#modal_puntos_ventas').modal('hide');
            $('#modal_new_punto_venta').modal('show');
        }

        function cancelarCreacionPuntoVenta(){
            $('#modal_new_punto_venta').modal('hide');
            $('#modal_puntos_ventas').modal('show');
        }

        function generarCuis(){
            Swal.fire({
                title: "Esta seguro de generar un CUIS par el Punto de Venta?",
                text: "Se verificar antes de crear!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, crear!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let datos = $('#formulario_genera_cuis').serializeArray()
                        $.ajax({
                            url   : "{{ url('empresa/crearCuis') }}",
                            method: "POST",
                            data  : datos,
                            success: function (data) {
                                console.log(data)
                                if(data.estado === 'success'){
                                    Swal.fire({
                                        icon:'success',
                                        title: "EXITO!",
                                        text:  data.text,
                                    })
                                }else if( data.estado === 'warnig'){
                                    Swal.fire({
                                        icon : 'warning',
                                        title: "ALTERTA!",
                                        text : data.text,
                                        timer: 5000
                                    })
                                }else{
                                    Swal.fire({
                                        icon : 'error',
                                        title: "SE GENERO UN ERROR!",
                                        text : JSON.stringify(data.msg),
                                        timer: 10000
                                    })
                                }
                                $('#modal_genera_cuis').modal('hide')
                            }
                        })
                    }
                });
        }

        function modal_genera_cuis(punto_venta, sucursal){

            $('#codigo_punto_venta_id_cuis').val(punto_venta)
            $('#codigo_sucursal_id_cuis').val(sucursal)

            $('#modal_puntos_ventas').modal('hide')
            $('#modal_genera_cuis').modal('show');
        }

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


   </script>
@endsection


