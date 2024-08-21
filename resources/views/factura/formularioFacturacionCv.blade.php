@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Formulario de Facturacion Compra y Venta</h1>
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
                    <button class="btn btn-sm fw-bold btn-primary" onclick="modalRol()">Nuevo Rol</button>
                </div> --}}
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
                                            {{-- CUIS: {{ (session()->has('scuis'))?  session('scuis') : '<span class="text-danger">NO existe la Cuis Vigente</span>'}} --}}
                                        </div>
                                        <div class="col-md-3">
                                            @if ($cufd)
                                                {{-- CUFD: {{ $cufd->codigo_control." ".str_replace("T", " ",substr(session('sfechaVigenciaCufd'), 0 , 16)) }} --}}
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
                        {{--  <div id="tabla_ventas" style="display: none">  --}}
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
                                        <input type="number" readonly class="form-control form-control-sm" id="precio_venta" name="precio_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                        <input type="number" class="form-control form-control-sm" id="cantidad_venta" name="cantidad_venta" value="0" min="1" required onkeyup="multiplicarPrecioAlTolta()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Total</label>
                                        <input type="number" class="form-control form-control-sm" id="total_venta" name="total_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-circle btn-sm btn-icon mt-9" type="button" onclick="mostraBloqueMasDatosProdcuto()" title="Mostrar mas opcion"><i class="fa fa-note-sticky"></i> +</button>
                                        <button class="btn btn-success btn-circle btn-sm btn-icon mt-9" type="button" onclick="agregarProducto()" title="Agregar al Carro de compras"><i class="fa fa-shopping-cart"></i> +</button>
                                    </div>

                                    {{--  <div class="col-md-8">
                                        <label class="required fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                        <input type="text" class="form-control form-control-sm" id="descripcion_adicional" name="descripcion_adicional" required>
                                    </div>  --}}
                                </div>
                                <div class="row" style="display: none;" id="bloque_mas_datos_productos">
                                    <div class="col-md-6">
                                        <label class="required fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                        <input type="text" class="form-control form-control-sm" id="descripcion_adicional" name="descripcion_adicional" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="required fw-semibold fs-6 mb-2">Numero Serie</label>
                                        <input type="number" class="form-control form-control-sm" id="numero_serie" name="numero_serie" min="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="required fw-semibold fs-6 mb-2">Codigo Imei</label>
                                        <input type="number" class="form-control form-control-sm" id="codigo_imei" name="codigo_imei" min="1">
                                    </div>
                                    {{--  <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                        <input type="number" readonly class="form-control form-control-sm" id="precio_venta" name="precio_venta" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                        <input type="number" class="form-control form-control-sm" id="cantidad_venta" name="cantidad_venta" value="0" min="1" required onkeyup="multiplicarPrecioAlTolta()">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="required fw-semibold fs-6 mb-2">Total</label>
                                        <input type="number" class="form-control form-control-sm" id="total_venta" name="total_venta" value="0" min="1" required>
                                    </div>

                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-circle btn-sm btn-icon mt-9" type="button" onclick="" title="Agregar al Carro de compras"><i class="fa fa-note-sticky"></i> +</button>
                                        <button class="btn btn-success btn-circle btn-sm btn-icon mt-9" type="button" onclick="agregarProducto()" title="Agregar al Carro de compras"><i class="fa fa-shopping-cart"></i> +</button>
                                    </div>  --}}
                                </div>
                            </form>
                            <hr>
                            {{--  <div id="tabla_detalles" style="display: none">  --}}
                            <div id="tabla_detalles">
                                <h2>Carrito de Compras</h2>
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
                            <div class="row">
                                {{--  <div class="col-md-8">  --}}
                                <div class="col-md-12">
                                    <button class="btn btn-info btn-sm w-100" onclick="mostrarFormularioClientes()">Seleccionar Cliente <i class="fa fa-user-alt"></i></button>
                                    {{--  <h4 class="text-info text-center">SELECCIONAR CLIENTE</h4>  --}}
                                </div>
                                {{--  <div class="col-md-4">
                                    <button class="btn btn-dark btn-sm btn-icon btn-circle"><i class="fa fa-user-alt"></i></button>
                                </div>  --}}
                            </div>
                            <form id="formulario_cliente_escogido" style="display: none">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Cedula</label>
                                        <input type="text" class="form-control form-control-sm buscar-persona" name="cedula_escogido" id="cedula_escogido">
                                        <input type="text" name="cliente_id_escogido" id="cliente_id_escogido">
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

                            {{--  <div id="bloque_cliente_escogido" style="display:none">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Cedula</label>
                                        <input type="text" name="cedula_seleccionado" id="cedula_seleccionado" class="form-control form-control-sm" readonly>
                                        <input type="text" name="cliente_id_seleccionado" id="cliente_id_seleccionado">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Nombres</label>
                                        <input type="text" name="nombre_seleccionado" id="nombre_seleccionado" class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap. Paterno</label>
                                        <input type="text" name="ap_paterno_seleccionado" id="ap_paterno_seleccionado" class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="fs-6 fw-semibold form-label mb-2">Ap. Mateerno</label>
                                        <input type="text" name="ap_materno_seleccionado" id="ap_materno_seleccionado" class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>  --}}

                        </div>

                        <hr>

                        <div class="row" id="bloque_facturacion" style="display: none;">
                            <div class="col-md-12">
                                <button class="btn btn-sm w-100 btn-dark" onclick="muestraDatosFactura()">FACTURAR</button>
                            </div>
                        </div>

                        <hr>

                        <div id="bloqueDatosFactura" style="display: none">
                            <h3>DATOS PARA FACTURA</h3>
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
                                        {{--  <input type="text" class="form-control" id="razon_factura" name="razon_factura" autocomplete="off" required value="{{ $razon_social }}">  --}}
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
                                {{-- <h3 class="text-center text-info">PAGO</h3> --}}
                                {{-- <div class="row" style="display: none" id="bloque_exepcion"> --}}
                                <div class="row" id="bloque_exepcion">
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
                                    {{-- <button class="btn btn-sm w-100 btn-success" onclick="emitirFactura()" id="boton_enviar_factura"> <i class="fa fa-spinner fa-spin"></i>Enviar</button> --}}
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

        var arrayProductos = [];
        var arrayPagos     = [];
        var table;
        var arrayProductoCar = [];

        $(document).ready(function() {

            // ajaxListadoTipoDocumentoSector();
            // ajaxListadoClientes();
            //ajaxListadoServicios();

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

                // var valor = $(this).val();
                // var nombreInput = $(this).attr('name');
                // console.log('Valor:', valor, 'Nombre del input:', nombreInput);
            });

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

            var json = JSON.parse(selected.value);

            $('#precio_venta').val(json.precio)
            $('#cantidad_venta').val(1)
            $('#total_venta').val((1*json.precio))
            $('#numero_serie').val(json.numero_serie)
            $('#codigo_imei').val(json.codigo_imei)

        }

        function agregarProducto(){
            var servicioDatos = JSON.parse($("#serivicio_id_venta").val())

            let id            = servicioDatos.id;
            var filaExistente = table.row("#producto-" + id);
            var precio        = servicioDatos.precio;
            var cantidad      = $('#cantidad_venta').val();
            var total         = precio*cantidad;
            var subTotal      = (precio*cantidad)-0;

            let servicio = {
                servicio_id          : servicioDatos.id,
                descripcion          : servicioDatos.descripcion,
                precio               : parseFloat(servicioDatos.precio).toFixed(2),
                numero_serie         : $("#numero_serie").val(),
                numero_imei          : $("#codigo_imei").val(),
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
                    servicioDatos.descripcion,
                    precio,
                    "<span class='cantidad'>"+cantidad+"</span>",
                    "<span class='total'>"+total+"</span>",
                    '<input class="form-control form-control-sm" type="text" name="descuento_'+id+'" id="descuento_'+id+'" value="0" onchange="ejecutarDescuento(this)">',
                    "<span class='subTotal'>"+subTotal+"</span>",
                    "<button class='eliminar btn btn-icon btn-danger btn-circle btn-sm'><i class='fa fa-trash'></button>"
                ]).node().id = 'producto-' + id;
                table.draw(false);

                // AGREGAMOS AL CARRO LOS PRODUSTOS
                arrayProductoCar.push(servicio);
                // AGREGAMOS AL CARRO LOS PRODUSTOS

                $('#monto_total').val(parseFloat($('#monto_total').val())+parseFloat(servicio.subTotal))
            }






            /*

            if($("#formulario_venta")[0].checkValidity()){
                let datoscombi = $('#formulario_venta, #formulario_cliente_escogido').serializeArray();
                $.ajax({
                    url: "{{ url('factura/agregarProducto') }}",
                    type    : 'POST',
                    data    : datoscombi,
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            let cliente = $('#cliente_id_escogido').val();
                            ajaxListadoDetalles(cliente);
                            $('#tabla_detalles').show('toogle')
                        }
                    }
                });
            }else{
                $("#formulario_venta")[0].reportValidity();
            }
            */

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
                        // servicio.cantidad = parseInt(servicio.cantidad) + parseInt(cantidad);
                        // servicio.descuento = parseFloat(servicio.descuento) + parseFloat(valorDescuento);
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

                /*
                console.log(
                    $('#cedula_escogido').val().length,
                    $('#nombre_escogido').val().length,
                    $('#ap_paterno_escogido').val().length,
                    $('#ap_materno_escogido').val().length,
                    (
                        $('#cedula_escogido').val().length > 3 ||
                        $('#nombre_escogido').val().length > 3 ||
                        $('#ap_paterno_escogido').val().length > 3 ||
                        $('#ap_materno_escogido').val().length > 3
                    )
                )
                */

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
            $('#nombre_escogido').val(nombres);
            $('#ap_paterno_escogido').val(ap_paterno);
            $('#ap_materno_escogido ').val(ap_materno);
            $('#cedula_escogido').val(cedula);

            $('#nit_factura').val(nit);
            $('#razon_factura').val(razon_social);

            /*
            $('#cliente_id_escogido').attr('readonly', true);
            $('#nombre_escogido').attr('readonly', true);
            $('#ap_paterno_escogido').attr('readonly', true);
            $('#ap_materno_escogido ').attr('readonly', true);
            $('#cedula_escogido').attr('readonly', true);
            */

            //$('#formulario_cliente_escogido').hide('toogle')
            $('#tabla-clientes-buscados').hide('toggle')
            $('#bloque_facturacion').toggle('show')

            //$('#bloque_cliente_escogido').show('toogle')

            /*

            $('#cliente_id_seleccionado').val(cliente);
            $('#nombre_seleccionado').val(nombres);
            $('#ap_paterno_seleccionado').val(ap_paterno);
            $('#ap_materno_seleccionado ').val(ap_materno);
            $('#cedula_seleccionado').val(cedula);

            $('#formulario_cliente_escogido').hide('toogle')
            $('#tabla-clientes-buscados').hide('toogle')
            $('#bloque_cliente_escogido').show('toogle')
            */

            /*
            $('#tabla_clientes').hide('toogle')
            $('#tabla_ventas').show('toogle')

            ajaxListadoDetalles(cliente);
            */
        }

        function mostrarFormularioClientes(){
            $('#formulario_cliente_escogido').toggle('show');
            $('#tabla_detalles').toggle('hide');
        }

        function muestraDatosFactura(){

            $('#bloqueDatosFactura').show('toogle')

            // $('#bloqueDatosFactura').show('toggle')
            // $('#bloque_tipos_pagos').show('toggle')
            // $('#boton_enviar_factura').show('toggle')
            // $('#boton_enviar_recivo').hide('toggle')

            /*
            $.ajax({
                url: "{{ url('factura/arrayCuotasPagar') }}",
                data:{
                    cliente : $('#cliente_id_escogido').val()
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        arrayPagos     = data.detalles;
                        arrayProductos = JSON.parse(data.lista)
                        $('#bloqueDatosFactura').show('toogle')
                    }
                }
            });
            */
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
                                // $('#bloque_exepcion').hide('toggle');
                            }else{
                                $('#nitnoexiste').show('toggle')
                                $('#nitsiexiste').hide('toggle')
                                $('#execpcion').prop('checked', true);
                                // $('#bloque_exepcion').show('toggle');
                            }
                        }else{
                            $('#errorValidar').show('toggle')
                        }
                    }
                });

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

            // let cliente = $('#cliente_id_escogido').val()

            $.ajax({
                url   : "{{ url('factura/emitirFacturaCv') }}",
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
                    uso_cafc                          : $('#uso_cafc').val(),
                    numero_factura_cafc               : $('#numero_factura_cafc').val(),
                    execpcion                         : $('#execpcion').is(':checked'),
                    complemento                       : $('#complemento').val(),
                    descuento_adicional               : $('#descuento_adicional').val(),
                    monto_total                       : $('#monto_total').val(),
                },
                success: function (data) {
                    // if(data.estado === 'success'){
                    //     if(data.cantidad > 0)
                    //         $('#tabla-clientes-buscados').show('toogle')

                    //     $('#tabla-clientes-buscados').html(data.listado)
                    // }else{

                    // }
                },
                error: function(error){
                    if (error.status === 419) {
                        alert('Tu sesión ha expirado. Por favor, vuelve a cargar la página.');
                        // Opcional: Recargar la página
                        location.reload();
                    } else {
                        // Manejar otros errores
                    }
                }
            })


            // console.log(cliente, arrayProductoCar);


            // $.ajax({
            //     url: "{{ url('factura/verificaItemsGeneracion') }}",
            //     data: {
            //         cliente: cliente
            //     },
            //     type: 'POST',
            //     dataType:'json',
            //     success: function(data) {

            //         // console.log(data)

            //         if(data.estado === "success"){
            //             if(data.cantidad == 0){
            //                 Swal.fire({
            //                     icon:   'error',
            //                     title:  'Error!',
            //                     text:   "DEBE AL MENOS AGREGAR UN SERVICIO/PRODUCTO",
            //                     timer: 5000
            //                 })
            //             }else{

            //                 // if($("#formularioGeneraFactura")[0].checkValidity() && $("#formulario_tipo_pagos")[0].checkValidity()){
            //                 if($("#formularioGeneraFactura")[0].checkValidity()){


            //                     // // Obtén el botón y el icono de carga
            //                     // var boton = $("#boton_enviar_factura");
            //                     // var iconoCarga = boton.find("i");
            //                     // // Deshabilita el botón y muestra el icono de carga
            //                     // boton.attr("disabled", true);
            //                     // iconoCarga.show();

            //                     //PONEMOS TODO AL MODELO DEL SIAT EL DETALLE
            //                     detalle = [];

            //                     // console.log($('#numero_serie').val());
            //                     // console.log($('#codigo_imei').val());

            //                     // console.log("----------------------");
            //                     // console.log(arrayProductos,arrayProductos[0].numero_serie == null , arrayProductos[0].codigo_imei == null);
            //                     // console.log("----------------------");

            //                     arrayProductos.forEach(function (prod){
            //                         detalle.push({
            //                             actividadEconomica  :   prod.codigo_caeb,
            //                             codigoProductoSin   :   prod.codigo_producto,
            //                             codigoProducto      :   prod.servicio_id,
            //                             descripcion         :   prod.descripcion,
            //                             cantidad            :   prod.cantidad,
            //                             unidadMedida        :   prod.codigo_clasificador,
            //                             precioUnitario      :   prod.precio,
            //                             montoDescuento      :   prod.descuento,
            //                             subTotal            :   ((prod.cantidad*prod.precio)-prod.descuento),
            //                             numeroSerie         :   (prod.numero_serie == null)? null : prod.numero_serie,
            //                             numeroImei          :   (prod.codigo_imei == null)? null : prod.codigo_imei
            //                         })
            //                     })

            //                     console.log(detalle, arrayProductos, arrayPagos)


            //                     // let numero_factura                  = $('#numero_factura').val();
            //                     let numero_factura                  = null;
            //                     // let cuf                             = "123456789";//cambiar
            //                     // let cufd                            = "{{ session('scufd') }}";  //solo despues de que aga
            //                     // let direccion                       = "{{ session('sdireccion') }}";//solo despues de que aga
            //                     let cuf                             = null;//cambiar
            //                     let cufd                            = null;  //solo despues de que aga
            //                     let direccion                       = null;//solo despues de que aga
            //                     var tzoffset                        = ((new Date()).getTimezoneOffset()*60000);
            //                     let fechaEmision                    = ((new Date(Date.now()-tzoffset)).toISOString()).slice(0,-1);
            //                     let nombreRazonSocial               = $('#razon_factura').val();
            //                     let codigoTipoDocumentoIdentidad    = $('#tipo_documento').val()
            //                     let numeroDocumento                 = $('#nit_factura').val();

            //                     let complemento;
            //                     // var complementoValue = $("#complemento").val();
            //                     // if (complementoValue === null || complementoValue.trim() === ""){
            //                     //     complemento                     = null;
            //                     // }else{
            //                     //     if($('#tipo_documento').val()==5){
            //                     //         complemento                     = null;
            //                     //     }else{
            //                     //         complemento                     = $('#complemento').val();
            //                     //     }
            //                     // }

            //                     let montoTotal                      = $('#total_a_pagar_importe').val();
            //                     let descuentoAdicional              = $('#descuento_adicional_global').val();
            //                     let leyenda                         = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
            //                     let usuario                         = "{{ Auth::user()->name }}";

            //                     let codigoExcepcion;
            //                     if ($('#execpcion').is(':checked'))
            //                         codigoExcepcion                 = 1;
            //                     else
            //                         codigoExcepcion                 = 0;


            //                     var factura = [];
            //                     factura.push({
            //                         cabecera: {
            //                             // nitEmisor                       :"{{ $empresa->nit }}",
            //                             // razonSocialEmisor               :'{{ $empresa->razon_social }}',
            //                             // municipio                       :"Santa Cruz",
            //                             // telefono                        :"73130500",

            //                             nitEmisor                       :null,
            //                             razonSocialEmisor               :null,
            //                             municipio                       :null,
            //                             telefono                        :null,
            //                             numeroFactura                   :numero_factura,
            //                             cuf                             :cuf,
            //                             cufd                            :cufd,
            //                             codigoSucursal                  :null,
            //                             direccion                       :direccion ,
            //                             codigoPuntoVenta                :null,
            //                             fechaEmision                    :fechaEmision,
            //                             nombreRazonSocial               :nombreRazonSocial,
            //                             codigoTipoDocumentoIdentidad    :codigoTipoDocumentoIdentidad,
            //                             numeroDocumento                 :numeroDocumento,
            //                             complemento                     :null,
            //                             // complemento                     :complemento,
            //                             codigoCliente                   :numeroDocumento,
            //                             codigoMetodoPago                :$('#facturacion_datos_tipo_metodo_pago').val(),
            //                             numeroTarjeta                   :null,
            //                             montoTotal                      :montoTotal,
            //                             montoTotalSujetoIva             :montoTotal,

            //                             codigoMoneda                    :$('#facturacion_datos_tipo_metodo_pago').val(),
            //                             tipoCambio                      :1,
            //                             montoTotalMoneda                :montoTotal,

            //                             montoGiftCard                   :null,
            //                             descuentoAdicional              :descuentoAdicional,//ver llenado
            //                             codigoExcepcion                 :codigoExcepcion,
            //                             cafc                            :null,
            //                             leyenda                         :leyenda,
            //                             usuario                         :usuario,
            //                             codigoDocumentoSector           :1
            //                         }
            //                     })

            //                     detalle.forEach(function (prod1){
            //                         factura.push({
            //                             detalle:prod1
            //                         })
            //                     })
            //                     var datos = {factura};
            //                     var datosCliente = {
            //                         'cliente_id': $('#cliente_id_escogido').val(),
            //                         'pagos'     : arrayPagos,
            //                         // 'empresa'   : "{{ $empresa->id }}"
            //                         // 'realizo_pago': $("#realizo_pago").prop("checked"),
            //                         'numero_cafc': $('#numero_factura_cafc').val(),
            //                         'uso_cafc'                : $('input[name="uso_cafc"]:checked').val(),
            //                     };
            //                     var datosRecepcion = {
            //                         // 'uso_cafc'                : $('input[name="uso_cafc"]:checked').val(),
            //                         // 'codigo_cafc_contingencia': $('#codigo_cafc_contingencia').val()
            //                     };
            //                     $.ajax({
            //                         url : "{{ url('factura/emitirFactura') }}",
            //                         data: {
            //                             datos         : datos,
            //                             datosCliente  : datosCliente,
            //                             // datosRecepcion: datosRecepcion,
            //                             modalidad     : $('#tipo_facturacion').val(),
            //                             tipo_pago     : $('#tipo_pago').val(),
            //                             monto_pagado  : $('#miInput').val(),
            //                             cambio        : $('#cambio').val()
            //                         },
            //                         type: 'POST',
            //                         dataType:'json',
            //                         success: function(data) {

            //                             if(data.estado === "VALIDADA"){
            //                                 Swal.fire({
            //                                     icon : 'success',
            //                                     title: 'Excelente!',
            //                                     text : 'LA FACTURA FUE VALIDADA',
            //                                     timer: 3000
            //                                 })
            //                                 window.location.href = "{{ url('factura/listado')}}"
            //                             }else if(data.estado === "error_email"){
            //                                 Swal.fire({
            //                                     icon : 'error',
            //                                     title: 'Error!',
            //                                     text : data.msg,
            //                                 })
            //                                 // Habilita el botón y oculta el icono de carga después de completar
            //                                 boton.attr("disabled", false);
            //                                 iconoCarga.hide();
            //                             }else if(data.estado === "OFFLINE"){
            //                                 Swal.fire({
            //                                     icon : 'warning',
            //                                     title: 'Exito!',
            //                                     text : 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
            //                                     timer: 2000
            //                                 })
            //                                 // window.location.href = "{{ url('factura/listado')}}"
            //                             }else{
            //                                 Swal.fire({
            //                                     icon : 'error',
            //                                     title: data.msg,
            //                                     text : 'LA FACTURA FUE RECHAZADA',
            //                                 })
            //                                 // Habilita el botón y oculta el icono de carga después de completar
            //                                 boton.attr("disabled", false);
            //                                 iconoCarga.hide();
            //                             }
            //                         }
            //                     });

            //                 }else{
            //                     $("#formularioGeneraFactura")[0].reportValidity();
            //                     // $("#formulario_tipo_pagos")[0].reportValidity()
            //                 }
            //             }
            //         }
            //     }
            // });
        }

        function ejecutarDescuentoAdicional(){
            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
            let descuentoAdicional = $('#descuento_adicional').val();
            $('#monto_total').val( parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
        }

        // function buscarServicioPorId(id_servicio){
        //     let dato = false;
        //     let servicio = arrayProductoCar.find(s => s.servicio_id === id_servicio);
        //     if (servicio) {
        //         servicio.cantidad = parseInt(servicio.cantidad) + parseInt(cantidad);
        //         dato = true;
        //     } else {
        //         dato = false;
        //     }

        //     console.log("DATO");
        //     console.log(dato);
        //     console.log("SERVCIO_ID");
        //     console.log(id_servicio);
        //     console.log("CANTIDAD");
        //     console.log(cantidad);
        //     console.log("SERVICIO");
        //     console.log(servicio);

        //     return dato;
        // }

        /*

        function multiplicarPrecioAlTolta(){
            let precio   = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            $('#total_venta').val((precio*cantidad));
            // console.log(precio, cantidad, (precio*cantidad))
        }

        function ajaxListadoDetalles(cliente){
            let datos = {cliente : cliente}
            $.ajax({
                url   : "{{ url('factura/ajaxListadoDetalles') }}",
                method: "POST",
                data  : datos,
                success: function (data) {
                    if(data.estado === 'success'){

                        if(data.cantidad > 0)
                            $('#tabla_detalles').show('toogle')

                        $('#tabla_detalles').html(data.listado)

                    }else{

                    }
                }
            })
        }

        function descuentoPorItem(detalle, element, total, cliente){

            let ope = total - element.value

            if(ope > 0){
                $.ajax({
                    url   : "{{ url('factura/descuentoPorItem') }}",
                    method: "POST",
                    data  : {
                        detalle : detalle,
                        descunto: element.value
                    },
                    success: function (data) {
                        if(data.estado === 'success'){

                            ajaxListadoDetalles(cliente)
                            $('#bloqueDatosFactura').hide('toogle')

                            // $('#tabla_detalles').html(data.listado)

                            // Swal.fire({
                            //     icon             : 'success',
                            //     title            : data.msg,
                            //     showConfirmButton: false,       // No mostrar botón de confirmación
                            //     timer            : 2000,        // 5 segundos
                            //     timerProgressBar : true
                            // });
                            // ajaxListadoTipoDocumentoSector();

                        }else{

                        }
                    }
                })
            }else{
                Swal.fire({
                    icon             : 'error',
                    title            : 'El descuento no puede exeder al precio del Item',
                    showConfirmButton: false,       // No mostrar botón de confirmación
                    timer            : 2000,        // 5 segundos
                    timerProgressBar : true
                });

                ajaxListadoDetalles(cliente)

            }

        }

        function eliminarDetalle(detalle, cliente){
            $.ajax({
                url   : "{{ url('factura/eliminarDetalle') }}",
                method: "POST",
                data  : {
                    detalle: detalle,
                },
                success: function (data) {
                    if(data.estado === 'success'){

                        ajaxListadoDetalles(cliente)
                        $('#bloqueDatosFactura').hide('toogle')

                    }else{

                    }
                }
            })
        }

        function descuentoAdicionalGlobal(){
            $.ajax({
                url   : "{{ url('factura/descuentoAdicionalGlobal') }}",
                method: "POST",
                data  : {
                    cliente: $('#cliente_id_escogido').val(),
                },
                success: function (data) {
                    if(data.estado === 'success'){
                        let desAdi = $('#descuento_adicional_global').val();
                        let total  = data.valor;
                        let dat = (total) - desAdi
                        if(dat > 0){
                            $('#total_a_pagar_importe').val(dat.toFixed(2))
                            $('#bloqueDatosFactura').hide('toogle')
                        }else{
                            Swal.fire({
                                icon             : 'error',
                                title            : 'El descuento no puede exeder al precio Total',
                                showConfirmButton: false,       // No mostrar botón de confirmación
                                timer            : 2000,        // 5 segundos
                                timerProgressBar : true
                            });

                            $('#descuento_adicional_global').val(0);
                        }

                    }else{

                    }
                }
            })
        }





        function bloqueCAFC(){
            if($('#tipo_facturacion').val() === "offline"){
                $('#bloque_cafc').show('toggle')
            }else{
                $('#bloque_cafc').hide('toggle')
            }
        }


        */

   </script>
@endsection


