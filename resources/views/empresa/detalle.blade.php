@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <!--begin::Modal - Adjust Balance-->
    <div class="modal fade" id="modal_lista_producto_servicios" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Lista Productos Servicios</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div id="tabla_lista_producto_servicios">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-dark w-100 btn-sm" onclick="volverPuntoVentasProductoServicios()">Volver Punto Ventas</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->


    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_sincronizar_productos_servicios" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario Sincronizar Lista Productos Servicios</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_sincronizar_actividades_economicas">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_genera_cuis" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente_cuis" id="codigo_ambiente_cuis" disabled>
                                    <option></option>
                                    <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                    <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                </select>
                                <input type="hidden" name="punto_venta_id_sincronizar_actividad" id="punto_venta_id_sincronizar_actividad">
                                <input type="hidden" name="sucuarsal_id_sincronizar_actividad" id="sucuarsal_id_sincronizar_actividad">
                                <input type="hidden" name="empresa_id_sincronizar_actividad" id="empresa_id_sincronizar_actividad" value="{{ $empresa->id }}">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-dark w-100 btn-sm" onclick="cancelarSincronizacion()">Cancelar</button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="sincronizarActividades()">Sincronizar</button>
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
    <div class="modal fade" id="modal_sincronizar_actividad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario Sincronizar Actividades Economimcas</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_sincronizar_actividades_economicas">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_genera_cuis" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente_cuis" id="codigo_ambiente_cuis" disabled>
                                    <option></option>
                                    <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                    <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                </select>
                                <input type="hidden" name="punto_venta_id_sincronizar_actividad" id="punto_venta_id_sincronizar_actividad">
                                <input type="hidden" name="sucuarsal_id_sincronizar_actividad" id="sucuarsal_id_sincronizar_actividad">
                                <input type="hidden" name="empresa_id_sincronizar_actividad" id="empresa_id_sincronizar_actividad" value="{{ $empresa->id }}">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-dark w-100 btn-sm" onclick="cancelarSincronizacion()">Cancelar</button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="sincronizarActividades()">Sincronizar</button>
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
    <div class="modal fade" id="modal_actividades_economicas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Actividades Economicass</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div id="tabla_activiades_economicas">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-dark w-100 btn-sm" onclick="volverPuntoVentas()">Volver Punto Ventas</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - New Card-->

    <!--end::Modal - New Card-->
    <div class="modal fade" id="modal_new_servicio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Servicio</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_servicio">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Actividad Economica Siat</label>
                                <select data-control="select2" name="actividad_economica_siat_id_new_servicio" id="actividad_economica_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($activiadesEconomica as $ae)
                                        <option value="{{ $ae->id }}">{{ $ae->descripcion }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="empresa_id_new_servicio" id="empresa_id_new_servicio" value="{{ $empresa->id }}">
                                <input type="hidden" name="servicio_id_new_servicio" id="servicio_id_new_servicio" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Producto Servicio Siat</label>
                                <select data-control="select2" name="producto_servicio_siat_id_new_servicio" id="producto_servicio_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($productoServicio as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->descripcion_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2">Unidad Medida Siat</label>
                                <select data-control="select2" name="unidad_medida_siat_id_new_servicio" id="unidad_medida_siat_id_new_servicio" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_servicio" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                    <option></option>
                                    @foreach ($unidadMedida as $um)
                                        <option value="{{ $um->id }}">{{ $um->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descripcion del Servico</label>
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
    <div class="modal fade" id="modal_new_usuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de usuario</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_new_usuario_empresa">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombres</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombres_new_usuaio_empresa" id="nombres_new_usuaio_empresa" required>
                                <input type="hidden" name="empresa_id_new_usuario_empresa" id="empresa_id_new_usuario_empresa" value="{{ $empresa->id }}">
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Ap Paterno</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="ap_paterno_new_usuaio_empresa" id="ap_paterno_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Ap Materno</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="ap_materno_new_usuaio_empresa" id="ap_materno_new_usuaio_empresa" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Usuario / Correo</label>
                                <input type="email" class="form-control fw-bold form-control-solid" name="usuario_new_usuaio_empresa" id="usuario_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Contraseña</label>
                                <input type="password" class="form-control fw-bold form-control-solid" name="contrasenia_new_usuaio_empresa" id="contrasenia_new_usuaio_empresa" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Numero de Celular</label>
                                <input type="number" class="form-control fw-bold form-control-solid" name="num_ceular_new_usuaio_empresa" id="num_ceular_new_usuaio_empresa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Rol</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_new_usuario" data-hide-search="true" class="form-select form-select-solid fw-bold" name="rol_id_new_usuaio_empresa" id="rol_id_new_usuaio_empresa">
                                    <option></option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->nombres }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="guardarUsuarioEmpresa()">Generar</button>
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
                                <input type="hidden" name="codigo_punto_venta_id_cuis" id="codigo_punto_venta_id_cuis">
                                <input type="hidden" name="codigo_sucursal_id_cuis" id="codigo_sucursal_id_cuis">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-dropdown-parent="#modal_genera_cuis" data-hide-search="true" class="form-select form-select-solid fw-bold" name="modalidad_cuis" id="modalidad_cuis" disabled>
                                    <option></option>
                                    <option value="1" {{ ($empresa->codigo_modalidad == 1)? 'selected' : '' }}>Electronica en Linea</option>
                                    <option value="2" {{ ($empresa->codigo_modalidad == 2)? 'selected' : '' }}>Computarizada en Linea</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-dark w-100 btn-sm" onclick="volverPuntoVenta()">Volver Punto Venta</button>
                            </div>
                            <div class="col-md-6">
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
                    <form id="formulario_punto_venta">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Ambiente</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_ambiente_punto_venta" id="codigo_ambiente_punto_venta" data-dropdown-parent="#modal_new_punto_venta" disabled>
                                    <option></option>
                                    <option value="2" {{ ($empresa->codigo_ambiente == 2)? 'selected' : '' }}>Desarrollo</option>
                                    <option value="1" {{ ($empresa->codigo_ambiente == 1)? 'selected' : '' }}>Produccion</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2">Modalidad</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_modalidad_punto_venta" id="codigo_modalidad_punto_venta" data-dropdown-parent="#modal_new_punto_venta" disabled>
                                    <option></option>
                                    <option value="1" {{ ($empresa->codigo_modalidad == 1)? 'selected' : '' }}>Electronica en Linea</option>
                                    <option value="2" {{ ($empresa->codigo_modalidad == 2)? 'selected' : '' }}>Computarizada en Linea</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Nombre</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="nombre_punto_venta" id="nombre_punto_venta" required>
                                <input type="hidden" name="sucursal_id_punto_venta" id="sucursal_id_punto_venta" required>
                                <input type="hidden" name="empresa_id_punto_venta" id="empresa_id_punto_venta" required value="{{ $empresa->id }}">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Descripcion Punto Venta</label>
                                <input type="text" class="form-control fw-bold form-control-solid" name="descripcion_punto_venta" id="descripcion_punto_venta" required>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="fs-6 fw-semibold form-label mb-2 required">Tipo Punto Venta</label>
                                <select data-control="select2" data-placeholder="Seleccione" data-hide-search="true" class="form-select form-select-solid fw-bold" name="codigo_tipo_punto_id_punto_venta" id="codigo_tipo_punto_id_punto_venta" data-dropdown-parent="#modal_new_punto_venta" required>
                                    <option></option>
                                    @foreach ( $siat_tipo_ventas as $tpv)
                                        <option value="{{ $tpv->codigo_clasificador }}">{{$tpv->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <button class="btn btn-sm btn-success w-100" onclick="guardarPuntoVenta()">Guardar</button>
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
                                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_tabla_usuario">
                                        <i class="ki-duotone ki-calendar-8 fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>Usuarios</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_tabla_siat_productos">
                                        <i class="ki-duotone ki-save-2 fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Siat Prodcutos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_tabla_servicios">
                                        <i class="ki-duotone ki-save-2 fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Servicios</a>
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
                                    <div class="tab-pane fade" id="tab_tabla_usuario" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="tabla_usuario_empres">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab_tabla_siat_productos" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="tabla_siat_productos">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end:::Tab pane-->
                                    <!--begin:::Tab pane-->
                                    <div class="tab-pane fade" id="tab_tabla_servicios" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="tabla_servicios">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end:::Tab pane-->
                                    <!--begin:::Tab pane-->
                                    <div class="tab-pane fade" id="kt_contact_view_activity" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="tabla_actividades">

                                                </div>
                                            </div>
                                        </div>
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
            ajaxListadoUsuarioEmpresa();
            ajaxListadoServicios();
            // ajaxListadoSiatProductosServicios();

        });

        function ajaxListadoSucursal(){
            let datos = {
                empresa : {{$empresa->id}}
            }
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

        function ajaxListadoUsuarioEmpresa(){
            let datos = {
                empresa: {{$empresa->id}}
            }
            $.ajax({
                // url   : "{{ url('empresa/ajaxListadoUsuarioEmpresa', [$empresa->id]) }}",
                url   : "{{ url('empresa/ajaxListadoUsuarioEmpresa') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_usuario_empres').html(data.listado)
                    }else{

                    }
                }
            })
        }

        function ajaxListadoServicios(){
            let datos = {
                empresa: {{$empresa->id}}
            }
            $.ajax({
                url   : "{{ url('empresa/ajaxListadoServicios') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_servicios').html(data.listado)
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
                        $('#sucursal_id_punto_venta').val(sucursal)
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

        function guardarPuntoVenta(){

            if($("#formulario_punto_venta")[0].checkValidity()){

                let datos = $('#formulario_punto_venta').serializeArray();

                $.ajax({
                    url   : "{{ url('empresa/guardaPuntoVenta') }}",
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
                            $('#modal_new_punto_venta').modal('hide');
                            $('#modal_puntos_ventas').modal('show');
                            $('#tabla_puntos_ventas').html(data.listado)
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
                $("#formulario_punto_venta")[0].reportValidity();
            }
        }

        function agregarUsuarioEmpresa(){
            // $('#empresa_id_new_usuario_empresa').val(0)
            $('#nombres_new_usuaio_empresa').val("")
            $('#ap_paterno_new_usuaio_empresa').val("")
            $('#ap_materno_new_usuaio_empresa').val("")
            $('#usuario_new_usuaio_empresa').val("")
            $('#contrasenia_new_usuaio_empresa').val("")
            $('#num_ceular_new_usuaio_empresa').val("")
            $('#modal_new_usuario').modal('show')
        }

        function guardarUsuarioEmpresa(){
            if($("#formulario_new_usuario_empresa")[0].checkValidity()){

                let datos = $('#formulario_new_usuario_empresa').serializeArray();

                $.ajax({
                    url   : "{{ url('empresa/guardarUsuarioEmpresa') }}",
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
                            // ajaxListadoSucursal();
                            ajaxListadoUsuarioEmpresa();
                            $('#modal_new_usuario').modal('hide');
                            // $('#modal_puntos_ventas').modal('show');
                            // $('#tabla_puntos_ventas').html(data.listado)
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
                $("#formulario_new_usuario_empresa")[0].reportValidity();
            }
        }

        function modalNuevoServicio(){
            $('#servicio_id_new_servicio').val(0)
            $('#modal_new_servicio').modal('show')
        }

        function sincronizarActividades(){

            let datos = $('#formulario_sincronizar_actividades_economicas').serializeArray()

            $.ajax({
                    url   : "{{ url('empresa/sincronizarActividades') }}",
                    method: "POST",
                    data  : datos,
                    success: function (data) {

                        console.log(data)

                        if(data.estado === 'success'){
                            // console.log(data)
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE SINCRONIZO CON EXITO",
                            })
                            // ajaxListadoSucursal();
                            $('#modal_sincronizar_actividad').modal('hide');
                            $('#modal_actividades_economicas').modal('show');
                            $('#tabla_activiades_economicas').html(data.listado)
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

        function ajaxListadoActiviadesEconomicas(punto_venta_id, sucursal_id){

            $.ajax({
                url   : "{{ url('empresa/ajaxListadoActiviadesEconomicas') }}",
                method: "POST",
                data  : {
                    empresa       : {{$empresa->id}},
                    punto_venta_id: punto_venta_id,
                    sucursal_id   : sucursal_id
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        // Swal.fire({
                        //     icon:'success',
                        //     title: "EXITO!",
                        //     text:  "SE REGISTRO CON EXITO",
                        // })
                        ajaxListadoSucursal();
                        $('#modal_actividades_economicas').modal('show')
                        $('#tabla_activiades_economicas').html(data.listado)
                        $('#modal_puntos_ventas').modal('hide')
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

        function modal_sincronizar_actividad(punto_venta_id, sucursal_id){
            console.log(punto_venta_id, sucursal_id)
            $('#punto_venta_id_sincronizar_actividad').val(punto_venta_id)
            $('#sucuarsal_id_sincronizar_actividad').val(sucursal_id)

            $('#modal_sincronizar_actividad').modal('show')
            $('#modal_actividades_economicas').modal('hide')

        }

        function cancelarSincronizacion(){
            $('#modal_sincronizar_actividad').modal('hide')
            $('#modal_actividades_economicas').modal('show')
        }

        function volverPuntoVentas(){
            $('#modal_actividades_economicas').modal('hide')
            $('#modal_puntos_ventas').modal('show')
        }

        function volverPuntoVenta(){
            $('#modal_genera_cuis').modal('hide')
            $('#modal_puntos_ventas').modal('show')
        }

        function ajaxRecuperarPuntosVentasSelect(elemt){
            $.ajax({
                url   : "{{ url('empresa/ajaxRecuperarPuntosVentasSelect') }}",
                method: "POST",
                data  : {
                    sucursal_id   : elemt.value
                },
                success: function (data) {
                    if(data.estado === 'success')
                        $('#new_servicio_bloque_sucursal_id').html(data.select)
                }
            })
        }

        function ajaxRecupraActividadesSelect(elemt){
            $.ajax({
                url   : "{{ url('empresa/ajaxRecupraActividadesSelect') }}",
                method: "POST",
                data  : {
                    punto_venta_id: elemt.value,
                    empresa_id    : $('#new_servicio_empresa_id').val(),
                    sucursal_id   : $('#new_servicio_sucursal_id').val()
                },
                success: function (data) {
                    if(data.estado === 'success')
                        $('#new_servicio_bloque_actividad').html(data.select)
                }
            })
        }

        function ajaxListadoSiatProductosServicios(punto_venta , sucursal){
            $.ajax({
                url   : "{{ url('empresa/ajaxListadoSiatProductosServicios') }}",
                method: "POST",
                data  : {
                    empresa_id : {{$empresa->id}},
                    punto_venta: punto_venta,
                    sucursal   : sucursal,
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#modal_lista_producto_servicios').modal('show')
                        $('#modal_puntos_ventas').modal('hide')
                        $('#tabla_lista_producto_servicios').html(data.listado)
                    }
                }
            })
        }

        function sincronizarSiatProductoServicios(punto_venta, sucursal){
            // $('#modal_sincronizar_productos_servicios').modal('show')

            $.ajax({
                url   : "{{ url('empresa/sincronizarSiatProductoServicios') }}",
                method: "POST",
                data  : {
                    empresa_id : {{$empresa->id}},
                    punto_venta: punto_venta,
                    sucursal   : sucursal,
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_lista_producto_servicios').html(data.listado)
                    }
                }
            })
        }

        function volverPuntoVentasProductoServicios(){
            $('#modal_lista_producto_servicios').modal('hide')
            $('#modal_puntos_ventas').modal('show')
        }

        function sincronizarPuntosVentas(sucursal){
            $.ajax({
                url   : "{{ url('empresa/sincronizarPuntosVentas') }}",
                method: "POST",
                data  : {
                    empresa_id : "{{$empresa->id}}",
                    sucursal   : sucursal,
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        $('#tabla_puntos_ventas').html(data.listado)
                    }
                }
            })
        }

        function guardarNewServioEmpresa(){

            if($("#formulario_new_servicio")[0].checkValidity()){
                let datos = $('#formulario_new_servicio').serializeArray();
                $.ajax({
                    url   : "{{ url('empresa/guardarNewServioEmpresa') }}",
                    method: "POST",
                    data  : datos,
                    success: function (data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: "EXITO!",
                                text:  "SE REGISTRO CON EXITO",
                            })
                            ajaxListadoServicios();
                            $('#modal_new_servicio').modal('hide');
                        }else{

                        }
                    }
                })
            }else{
                $("#formulario_new_servicio")[0].reportValidity();
            }
        }
   </script>
@endsection


