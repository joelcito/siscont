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
        <div class="modal-dialog modal-dialog-centered mw-900px">
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
                                <input type="hidden" name="cliente_id_cliente_new_usuaio_empresa" id="cliente_id_cliente_new_usuaio_empresa" required value="0">
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
                                <input type="number" class="form-control fw-bold form-control-solid" name="complemento_cliente_new_usuaio_empresa" id="complemento_cliente_new_usuaio_empresa">
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
                                <button type="button" class="btn btn-success w-100 btn-sm" onclick="guardarClienteEmpresa()">Agregar Usuario</button>
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
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Formulario de Facturacion Tasa Cero</h1>
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
                            <div class="col-md-12 text-center">
                                @if ($verificacionSiat->estado === "success")
                                    <div class="row">
                                        <div class="col-md-6 text-center">
                                            <span class="badge bg-success text-white w-100">{{ $verificacionSiat->resultado->RespuestaComunicacion->mensajesList->descripcion }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            @if ($cuis)
                                                CUIS: {{ $cuis->codigo }}
                                            @else
                                                <span class="badge badge-danger">NO existe un Cuis Vigente para este Usuario</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            @if ($cufd)
                                                CUFD: {{ $cufd->codigo_control." ".$cufd->fecha_vigencia }}
                                            @else
                                                <span class="badge badge-danger">NO existe un Cufd Vigente para este Usuario</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-danger text-white w-100">NO HAY CONECCION CON SIAT</span>
                                @endif
                            </div>
                        </div>
                        <div id="tabla_clientes">
                        </div>
                        <hr>
                        <div id="tabla_ventas">
                            <form id="formulario_venta">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="required fw-semibold fs-6 mb-2">Servicio / Producto</label>
                                        <select name="serivicio_id_venta" id="serivicio_id_venta" class="form-control form-control-sm" onchange="identificaSericio(this)" required>
                                            <option value="">SELECCIONE</option>
                                            @foreach ($servicios as $s)
                                            <option value="{{ $s }}">{{ $s->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                        <input type="number" class="form-control form-control-sm" id="precio_venta" name="precio_venta" value="0" min="1" required onchange="calcularPrecioTotal()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                        <input type="number" class="form-control form-control-sm" id="cantidad_venta" name="cantidad_venta" value="0" min="1" required onkeyup="calcularPrecioTotal()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Total</label>
                                        <input type="number" class="form-control form-control-sm" id="total_venta" name="total_venta" value="0" min="1" required readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-circle btn-sm btn-icon mt-9" type="button" onclick="mostraBloqueMasDatosProdcuto()" title="Mostrar mas opcion"><i class="fa fa-note-sticky"></i> +</button>
                                        <button class="btn btn-success btn-circle btn-sm btn-icon mt-9" type="button" onclick="agregarProducto()" title="Agregar al Carro de compras"><i class="fa fa-shopping-cart"></i> +</button>
                                    </div>
                                </div>
                                <div class="row" style="display: none;" id="bloque_mas_datos_productos">
                                    <div class="col-md-12">
                                        <label class="fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                        <input type="text" class="form-control form-control-sm" id="descripcion_adicional" name="descripcion_adicional">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div id="tabla_detalles" style="display: none">
                                <h2 class="text-center">CARRITO DE COMPRAS</h2>
                                <table id="carrito" class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th>Servicio / Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                            <th>Descuento</th>
                                            <th>Sub Total</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-semibold">
                                        <!-- Aquí se agregarán las filas del carrito -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5">Descuento Adicional</th>
                                            <th colspan="2">Monto Total</th>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <input class="form-control form-control-sm" name="descuento_adicional" id="descuento_adicional" type="number" value="0" onchange="ejecutarDescuentoAdicional()">
                                            </td>
                                            <td colspan="2">
                                                <input class="form-control form-control-sm" name="monto_total" id="monto_total" type="number" readonly value="0">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr>
                            <div class="row" id="bloque_seleccionar_cliente" style="display: none">
                                <div class="col-md-11">
                                    <button class="btn btn-info btn-sm w-100" onclick="mostrarFormularioClientes()"><span id="nombre_cliente"></span> <i class="fa fa-user-alt"></i></button>
                                </div>
                                <div class="col-md-1">
                                    <button title="Mostrar carro de compras" class="btn btn-dark btn-sm btn-circle btn-icon" onclick="mostrarCarritoVentas()"><i class="fa fa-shopping-basket"></i></button>
                                    <button title="Agregar cliente" class="btn btn-primary btn-sm btn-circle btn-icon" onclick="modalAgregarCliente()"><i class="fa fa-user-plus"></i></button>
                                </div>
                            </div>
                            <form id="formulario_cliente_escogido" style="display: none">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Cedula</label>
                                        <input type="text" class="form-control form-control-sm buscar-persona" name="cedula_escogido" id="cedula_escogido">
                                        <input type="hidden" name="cliente_id_escogido" id="cliente_id_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Nombre</label>
                                        <input type="text" class="form-control form-control-sm buscar-persona" name="nombre_escogido" id="nombre_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap Paterno</label>
                                        <input type="text" class="form-control form-control-sm buscar-persona" name="ap_paterno_escogido" id="ap_paterno_escogido">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                        <input type="text" class="form-control form-control-sm buscar-persona" name="ap_materno_escogido" id="ap_materno_escogido">
                                    </div>
                                </div>
                            </form>
                            <div id="tabla-clientes-buscados">

                            </div>
                        </div>
                        <hr>
                        <div class="row" id="bloque_facturacion" style="display: none;">
                            <div class="col-md-12">
                                <h2 class="text-center">DATOS DE LA FACTURA</h2>
                            </div>
                        </div>
                        <div id="bloqueDatosFactura" style="display: none">
                            <form id="formularioGeneraFactura">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">M. Pago</label>
                                        <select name="facturacion_datos_tipo_metodo_pago" id="facturacion_datos_tipo_metodo_pago" class="form-control form-control-sm" required>
                                            @foreach($tipoMetodoPago as $key => $value)
                                            <option value="{{ $value->tipo_clasificador }}" {{ ($value->tipo_clasificador == "1")? 'selected' :'' }}>{{ $value->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="">Tipo Moneda</label>
                                        <select name="facturacion_datos_tipo_moneda" id="facturacion_datos_tipo_moneda" class="form-control form-control-sm" required>
                                            @foreach($tipoMonedas as $key => $value)
                                            <option value="{{ $value->tipo_clasificador }}" {{ ($value->tipo_clasificador == "1")? 'selected' :'' }}>{{ $value->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="">Tipo Documento</label>
                                        <select name="tipo_documento" id="tipo_documento" class="form-control form-control-sm" onchange="verificaNit()" required>
                                            <option value="">SELECCIONE</option>
                                            @foreach ($tipoDocumento as $te)
                                                <option value="{{ $te->tipo_clasificador }}">{{ $te->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-2">
                                        <label for="">Nit/Cedula</label>
                                        <input type="number" class="form-control form-control-sm" id="nit_factura" name="nit_factura" onchange="verificaNit()" autocomplete="off" required>
                                        <small style="display: none;" class="text-danger" id="nitnoexiste">** NIT INVALIDO **</small>
                                        <small style="display: none;" class="text-success" id="nitsiexiste">** NIT VALIDO **</small>
                                        <small style="display: none;" class="text-danger" id="errorValidar">ERROR LA VALIDAR</small>
                                    </div>
                                    <div class="col-md-1" style="display: none" id="bloque_complemento">
                                        <label for="">Compl.</label>
                                        <input type="text" class="form-control form-control-sm" name="complemento" id="complemento" >
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">Razon Social</label>
                                        <input type="text" class="form-control form-control-sm" id="razon_factura" name="razon_factura" autocomplete="off" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Tipo Factura</label>
                                        <select name="tipo_facturacion" id="tipo_facturacion" class="form-control form-control-sm" onchange="bloqueCAFC()">
                                            <option value="online">En Linea</option>
                                            <option value="offline">Fuera de Linea</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" style="display: none;" id="bloque_cafc">
                                        <label for="">Uso del CAFC?</label>
                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <label for="radioNo">No</label>
                                                <input type="radio" name="uso_cafc" id="radioNo" value="No" checked>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="radioSi">Si</label>
                                                <input type="radio" name="uso_cafc" id="radioSi" value="Si">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="numero_fac_cafc" style="display: none;">
                                        <label for="">Numero de CAFC:</label>
                                        <input type="number" class="form-control form-control-sm" id="numero_factura_cafc" name="numero_factura_cafc">
                                    </div>
                                </div>
                                <div class="row" id="bloque_exepcion" style="display: none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Enviar con execpcion?</label>
                                            <input type="checkbox" name="execpcion" id="execpcion" readonly>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button class="btn btn-sm w-100 btn-success" onclick="emitirFactura()" id="boton_enviar_factura"> <i class="fa fa-spinner fa-spin" style="display:none;"></i>Enviar</button>
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
@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        var arrayProductos = [];
        var arrayPagos     = [];
        var table;
        var arrayProductoCar = [];

        $(document).ready(function() {

            $("#serivicio_id_venta").select2();

            // Inicializa el DataTable
            table = $('#carrito').DataTable({
                lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
                // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
                dom: '<"dt-head row"><"clear">t', // Use dom for basic layout
                language: {
                paginate: {
                    first : 'Primero',
                    last : 'Último',
                    next : 'Siguiente',
                    previous: 'Anterior'
                },
                search : 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
                },
                order:[],
                responsive: true
            });


            $('.buscar-persona').on('keyup', function() {

                ajaxListadoClientes();
            });


            $('input[name="uso_cafc"]').on('change', function() {
                verificarRadioSeleccionado();
            });

            $('#nombre_cliente').text('SELECCIONAR CLIENTE')

        });

        function ajaxListadoServicios(){
            let datos = {}
            $.ajax({
                url: "{{ url('factura/ajaxListadoServicios') }}",
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

        function identificaSericio(selected){

            if(selected.value != ''){
                var json = JSON.parse(selected.value);
                $('#precio_venta').val(json.precio)
                $('#cantidad_venta').val(1)
                $('#total_venta').val((1*json.precio))
                $('#numero_serie').val(json.numero_serie)
                $('#codigo_imei').val(json.codigo_imei)
            }else{
                $('#precio_venta').val(0)
                $('#cantidad_venta').val(0)
                $('#total_venta').val(0)
                $('#numero_serie').val(null)
                $('#codigo_imei').val(null)
                $('#descripcion_adicional').val(null)
            }

        }

        function agregarProducto(){

            if($("#formulario_venta")[0].checkValidity()){

                var servicioDatos = JSON.parse($("#serivicio_id_venta").val())

                let id            = servicioDatos.id;
                var filaExistente = table.row("#producto-" + id);
                // var precio        = servicioDatos.precio;
                var precio        = $('#precio_venta').val();
                var cantidad      = $('#cantidad_venta').val();
                var total         = precio*cantidad;
                var subTotal      = (precio*cantidad)-0;

                let servicio = {
                    servicio_id          : servicioDatos.id,
                    descripcion          : servicioDatos.descripcion,
                    precio               : parseFloat(precio).toFixed(2),
                    empresa_id           : servicioDatos.empresa_id,
                    cantidad             : parseInt($('#cantidad_venta').val()),
                    total                : parseFloat(total).toFixed(2),
                    descuento            : parseFloat(0).toFixed(2),
                    subTotal             : parseFloat(subTotal.toFixed(2)),
                    descripcion_adicional: $('#descripcion_adicional').val()
                }

                if (filaExistente.node()) {

                    // Si el producto ya está en el carrito, aumenta la cantidad en 2
                    var cantidadCell   = $(filaExistente.node()).find('.cantidad');
                    var cantidadActual = parseInt(cantidadCell.text());
                    var nuevaCantidad  = cantidadActual + parseInt(cantidad);
                    cantidadCell.text(nuevaCantidad);

                    // Actualiza el total
                    nuevoTotal = nuevaCantidad * precio
                    var totalCell = $(filaExistente.node()).find('.total');
                    totalCell.text((nuevoTotal).toFixed(2));

                    var subTotalCell  = $(filaExistente.node()).find('.subTotal');
                    var valorSubTotal = parseFloat(subTotalCell.text())
                    var nuevoSubTotal = nuevoTotal - parseFloat($('#descuento_'+id).val())
                    subTotalCell.text((nuevoSubTotal).toFixed(2));

                    let servicio = arrayProductoCar.find(s => s.servicio_id === servicioDatos.id);
                    if (servicio) {

                        servicio.cantidad              = parseFloat(servicio.cantidad) + parseFloat(cantidad);
                        servicio.total                 = parseFloat(nuevoTotal);
                        servicio.subTotal              = parseFloat(nuevoSubTotal);
                        servicio.descripcion_adicional = $('#descripcion_adicional').val();


                        let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                        let descuentoAdicional = $('#descuento_adicional').val()

                        $('#monto_total').val(parseFloat(sumaTotal)-parseFloat(descuentoAdicional))

                    } else {
                        Swal.fire({
                            icon:'error',
                            title: "ERROR!",
                            text:  "Error al actualizar el descuento.",
                            timer: 4000
                        })
                    }

                } else {
                    var subTotal      = precio*cantidad;

                    table.row.add([
                        servicioDatos.descripcion+" "+$('#descripcion_adicional').val(),
                        precio,
                        "<span class='cantidad'>"+cantidad+"</span>",
                        "<span class='total'>"+total+"</span>",
                        '<input class="form-control form-control-sm" type="text" name="descuento_'+id+'" id="descuento_'+id+'" value="0" onchange="ejecutarDescuento(this)">',
                        "<span class='subTotal'>"+subTotal+"</span>",
                        "<button class='eliminar btn btn-icon btn-danger btn-circle btn-sm' onclick='eliminarItem("+id+")' ><i class='fa fa-trash'></button>"
                    ]).node().id = 'producto-' + id;
                    table.draw(false);

                    // AGREGAMOS AL CARRO LOS PRODUSTOS
                    arrayProductoCar.push(servicio);
                    // AGREGAMOS AL CARRO LOS PRODUSTOS

                    $('#monto_total').val(parseFloat($('#monto_total').val())+parseFloat(servicio.subTotal))
                }

                // BORRAMOS LOS ITEM QUE AGREGAMOS
                $('#serivicio_id_venta').val(null).trigger('change');
                $('#tabla_detalles').show('toggle')
                $('#bloque_seleccionar_cliente').show('toggle')

            }else{
                $("#formulario_venta")[0].reportValidity();
            }

        }

        function ejecutarDescuento(valor){

            let valorDescuento = valor.value;
            let valorId        = valor.id;
            let id             = valorId.split("_")[1]
            var filaExistente  = table.row("#producto-" + id);

            if (filaExistente.node()) {
                var totalCell    = $(filaExistente.node()).find('.total');
                var valorTotal   = parseFloat(totalCell.text());
                var subTotalCell = $(filaExistente.node()).find('.subTotal');

                if(valorDescuento < valorTotal){
                    subTotalCell.text((valorTotal - valorDescuento).toFixed(2));
                    let servicio = arrayProductoCar.find(s => s.servicio_id === parseInt(id));
                    if (servicio) {
                        servicio.descuento = parseFloat(valorDescuento);
                        servicio.subTotal  = parseFloat(servicio.total) - parseFloat(valorDescuento);

                        // EJECUTAMOS EL DESCUENTO
                        let sumaTotal          = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                        let descuentoAdicional = $('#descuento_adicional').val()
                        $('#monto_total').val(parseFloat(sumaTotal)-parseFloat(descuentoAdicional))
                    } else {
                        Swal.fire({
                            icon:'error',
                            title: "ERROR!",
                            text:  "Error al actualizar el descuento",
                            timer: 4000
                        })
                    }

                }else{
                    Swal.fire({
                        icon:'error',
                        title: "ERROR!",
                        text:  "El valor de descuento no debe ser mayor al valor Total",
                        timer: 4000
                    })
                    $('#descuento_'+id).val(valorTotal-parseFloat(subTotalCell.text()))
                }

            } else {
                Swal.fire({
                    icon:'error',
                    title: "ERROR!",
                    text:  "Servicion no encontrado",
                    timer: 4000
                })
            }
        }

        function ajaxListadoClientes(){

            if(
                $('#cedula_escogido').val().length > 3 ||
                $('#nombre_escogido').val().length > 3 ||
                $('#ap_paterno_escogido').val().length > 3 ||
                $('#ap_materno_escogido').val().length > 3
            ){
                let datos = $('#formulario_cliente_escogido').serializeArray();
                $.ajax({
                    url   : "{{ url('factura/ajaxListadoClientesBusqueda') }}",
                    method: "POST",
                    data  : datos,
                    success: function (data) {
                        if(data.estado === 'success'){
                            if(data.cantidad > 0)
                                $('#tabla-clientes-buscados').show('toogle')

                            $('#tabla-clientes-buscados').html(data.listado)
                        }else{

                        }
                    }
                })
            }
        }

        function mostraBloqueMasDatosProdcuto(){

            $('#bloque_mas_datos_productos').toggle('show')
        }

        function escogerCliente(cliente,nombres, ap_paterno, ap_materno, cedula, nit, razon_social){

            $('#cliente_id_escogido').val(cliente);

            $('#nombre_escogido').val('');
            $('#ap_paterno_escogido').val('');
            $('#ap_materno_escogido ').val('');
            $('#cedula_escogido').val('');

            $('#nit_factura').val(nit);
            $('#razon_factura').val(razon_social);

            $('#tabla-clientes-buscados').hide('toggle')
            $('#bloque_facturacion').show('toggle')

            let nombreusuario = cedula+" | "+nombres+" | "+ap_paterno+" | "+ap_materno;

            $('#nombre_cliente').text(nombreusuario)

            $('#bloqueDatosFactura').show('toggle');
            $('#formulario_cliente_escogido').toggle('hide');
        }

        function mostrarFormularioClientes(){
            $('#formulario_cliente_escogido').toggle('show');
            $('#tabla_detalles').hide('toggle');
        }

        function muestraDatosFactura(){

            $('#bloqueDatosFactura').show('toogle')

        }

        function verificaNit(){
            if($('#tipo_documento').val()  === "5"){
                let nit = $('#nit_factura').val();
                $.ajax({
                    url: "{{ url('factura/verificarNit') }}",
                    data: {
                        nit: nit
                    },
                    type: 'POST',
                    dataType:'json',
                    success: function(data) {
                        if(data.estado == "success"){
                            if(data.estadoSiat){
                                $('#execpcion').prop('checked', false);
                                $('#nitsiexiste').show('toggle')
                                $('#nitnoexiste').hide('toggle')
                            }else{
                                $('#nitnoexiste').show('toggle')
                                $('#nitsiexiste').hide('toggle')
                                $('#execpcion').prop('checked', true);
                            }
                        }else{
                            $('#errorValidar').show('toggle')
                        }
                    }
                });

                $('#complemento').val(null)
                $('#bloque_complemento').hide('toggle')

            }else if($('#tipo_documento').val()  === "1"){

                $('#bloque_complemento').show('toggle')
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

            }else{
                $('#nitnoexiste').hide('toggle')
                $('#nitsiexiste').hide('toggle')
                $('#errorValidar').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#bloque_complemento').hide('toggle')

            }
        }

        function emitirFactura(){

            if($("#formularioGeneraFactura")[0].checkValidity()){

                if(arrayProductoCar.length > 0){

                    // Obtén el botón y el icono de carga
                    var boton = $("#boton_enviar_factura");
                    var iconoCarga = boton.find("i");
                    // Deshabilita el botón y muestra el icono de carga
                    boton.attr("disabled", true);
                    iconoCarga.show();

                    $.ajax({
                        url   : "{{ url('factura/emitirFacturaTc') }}",
                        method: "POST",
                        data  : {
                            cliente_id                        : $('#cliente_id_escogido').val(),
                            carrito                           : arrayProductoCar,
                            facturacion_datos_tipo_metodo_pago: $('#facturacion_datos_tipo_metodo_pago').val(),
                            facturacion_datos_tipo_moneda     : $('#facturacion_datos_tipo_moneda').val(),
                            tipo_documento                    : $('#tipo_documento').val(),
                            nit_factura                       : $('#nit_factura').val(),
                            razon_factura                     : $('#razon_factura').val(),
                            tipo_facturacion                  : $('#tipo_facturacion').val(),
                            uso_cafc                          : $('input[name="uso_cafc"]:checked').val(),
                            numero_factura_cafc               : $('#numero_factura_cafc').val(),
                            execpcion                         : $('#execpcion').is(':checked'),
                            complemento                       : $('#complemento').val(),
                            descuento_adicional               : $('#descuento_adicional').val(),
                            monto_total                       : $('#monto_total').val(),
                            monto_total                       : $('#monto_total').val(),
                        },
                        success: function (data) {
                            if(data.estado === "VALIDADA"){
                                Swal.fire({
                                    icon : 'success',
                                    title: 'Excelente!',
                                    text : 'LA FACTURA FUE VALIDADA',
                                    timer: 3000
                                }).then(() => {
                                    if(data.numero != null && data.numero != ''){
                                        window.open("{{ url('factura/generaPdfFacturaNewCv')}}/"+data.numero, "_blank", "width=800,height=600");
                                        window.location.reload();
                                    }else{
                                        window.location.href = "{{ url('factura/listado')}}"
                                    }
                                })
                            }else if(data.estado === "error_email"){
                                Swal.fire({
                                    icon : 'error',
                                    title: 'Error!',
                                    text : data.text,
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            }else if(data.estado === "OFFLINE"){
                                Swal.fire({
                                    icon : 'warning',
                                    title: 'Exito!',
                                    text : 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                                    timer: 2000
                                })
                                window.location.href = "{{ url('factura/listado')}}"
                            }else{
                                Swal.fire({
                                    icon : 'error',
                                    title: data.text,
                                    text : 'LA FACTURA FUE RECHAZADA',
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            }
                        },
                        error: function(error){
                            {{--  if (error.status === 419) {
                                alert('Tu sesión ha expirado. Por favor, vuelve a cargar la página.');
                                // Opcional: Recargar la página
                                location.reload();
                            } else {
                                // Manejar otros errores
                            }  --}}
                        }
                    })
                }else{
                    Swal.fire({
                        icon:   'error',
                        title:  'Error!',
                        text:   "Debe tener al menos un producto agregado al carrito!",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    })
                }
            }else{
                $("#formularioGeneraFactura")[0].reportValidity();
            }
        }

        function ejecutarDescuentoAdicional(){
            // let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
            // let descuentoAdicional = $('#descuento_adicional').val();
            // $('#monto_total').val( parseFloat(sumaTotal) - parseFloat(descuentoAdicional))

            let descuentoAdcional = parseFloat($('#descuento_adicional').val())
            let montoTotal        = parseFloat($('#monto_total').val())

            if(descuentoAdcional > -1){
                if(descuentoAdcional < montoTotal){
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = $('#descuento_adicional').val();
                    $('#monto_total').val( parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                }else{
                    Swal.fire({
                        icon : 'error',
                        title: "Error",
                        text : 'El descuento Adicional no debe ser mayor al monto total!',
                    })
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = 0;
                    $('#monto_total').val( parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                    $('#descuento_adicional').val(0);
                }
            }else{
                Swal.fire({
                    icon : 'error',
                    title: "Error",
                    text : 'El descuento debe ser mayor a 0!',
                })
                let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                let descuentoAdicional = 0;
                $('#monto_total').val( parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                $('#descuento_adicional').val(0);
            }
        }


        function bloqueCAFC(){
            if($('#tipo_facturacion').val() === "offline"){
                $('#bloque_cafc').show('toggle')
            }else{
                $('#bloque_cafc').hide('toggle')
            }
        }

        function verificarRadioSeleccionado() {
            var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();
            if (valorSeleccionado === 'No') {

                $('#numero_fac_cafc').hide('toggle');
                $('#numero_factura_cafc').val(0)

            } else if (valorSeleccionado === 'Si') {
                $.ajax({
                    url: "{{ url('factura/sacaNumeroCafcUltimo') }}",
                    method: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.estado === "success"){
                            $("#numero_factura_cafc").val(data.numero);
                            $('#numero_fac_cafc').show('toggle');
                        }else{
                            Swal.fire({
                                icon:   'error',
                                title:  'Error!',
                                text:   "Algo fallo"
                            })
                        }
                    }
                })
            }
        }

        function eliminarItem(id){

            var fila = table.row("#producto-" + id);
            var cantidadCell = $(fila.node()).find('.cantidad');
            var cantidadActual = parseInt(cantidadCell.text());

            // Reducir la cantidad en 1
            var nuevaCantidad = cantidadActual - 1;
            cantidadCell.text(nuevaCantidad);

            if (nuevaCantidad <= 0) {
                // Si la cantidad es 0 o menos, elimina la fila de la tabla
                table.row(fila).remove().draw(false);

                // Elimina el producto del array
                arrayProductoCar = arrayProductoCar.filter(s => s.servicio_id !== id);
            } else {
                // Si la cantidad sigue siendo mayor que 0, actualiza el total y el subTotal
                var precio = parseFloat($(fila.node()).find('.total').text()) / cantidadActual;
                var nuevoTotal = nuevaCantidad * precio;
                $(fila.node()).find('.total').text(nuevoTotal.toFixed(2));

                var subTotalCell = $(fila.node()).find('.subTotal');
                var descuento = parseFloat($('#descuento_' + id).val());
                var nuevoSubTotal = nuevoTotal - descuento;
                subTotalCell.text(nuevoSubTotal.toFixed(2));

                // Actualiza los valores en el array
                let servicio = arrayProductoCar.find(s => s.servicio_id === id);
                if (servicio) {
                    servicio.cantidad = nuevaCantidad;
                    servicio.total = nuevoTotal;
                    servicio.subTotal = nuevoSubTotal;
                }
            }

            // Actualizar el monto total
            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
            let descuentoAdicional = $('#descuento_adicional').val();
            $('#monto_total').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional));

        }

        function mostrarCarritoVentas(){
            $('#tabla_detalles').toggle('show')
        }

        function calcularPrecioTotal(){
            let precio = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            let total = parseFloat(precio) * parseFloat(cantidad);
            $('#total_venta').val(total)
        }

        function modalAgregarCliente(){
            $('#modal_new_cliente').modal('show');
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

                            $('#cliente_id_escogido').val(data.cliente);

                            let cedula     = $('#cedula_cliente_new_usuaio_empresa').val();
                            let nombres    = $('#nombres_cliente_new_usuaio_empresa').val();
                            let ap_paterno = $('#ap_paterno_cliente_new_usuaio_empresa').val();
                            let ap_materno = $('#ap_materno_cliente_new_usuaio_empresa').val();

                            let nombreusuario = cedula+" | "+nombres+" | "+ap_paterno+" | "+ap_materno;
                            $('#nombre_cliente').text(nombreusuario)

                            $('#nit_factura').val($('#nit_cliente_new_usuaio_empresa').val());
                            $('#razon_factura').val($('#razon_social_cliente_new_usuaio_empresa').val());

                            $('#bloqueDatosFactura').show('toggle');

                            //ajaxListado();
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

   </script>
@endsection


