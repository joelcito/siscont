<hr>
<h4 class="text-info text-center">DETALLES DE VENTA</h4>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_detalles">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>N</th>
            <th>Servicio</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th width="100px">Descuento</th>
            <th width="100px">Sub Total</th>
            <th width="50px">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @php
            $total = 0;
        @endphp
        @forelse ( $detalles as $key => $det)
            @php
                // $total = $total+ ($det->cantidad * $det->importe);
                $total = $total + $det->importe;
            @endphp
            <tr>
                <td>{{ ($key+1) }}</td>
                <td>{{ $det->servicio->descripcion." ".$det->descripcion_adicional }}</td>
                <td>{{ $det->precio }}</td>
                <td>{{ $det->cantidad }}</td>
                <td>{{ $det->total }}</td>
                <td>
                    <input type="number" class="form-control" max="{{ $det->total }}" value="{{ $det->descuento }}" onchange="descuentoPorItem('{{ $det->id }}', this, '{{ $det->total }}', '{{ $det->cliente_id }}')">
                </td>
                <td>{{ $det->importe }}</td>
                <td>
                    <button class="btn btn-danger btn-sm btn-icon" onclick="eliminarDetalle('{{ $det->id }}', '{{ $det->cliente_id }}')"><i class="fa fa-close"></i></button>
                </td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">
                <b>DESCUENTO ADICIONAL</b>
                <input type="number" class="form-control" value="0" onchange="descuentoAdicionalGlobal()" id="descuento_adicional_global" name="descuento_adicional_global">
            </th>
            <th colspan="2">
                <b>MONTO TOTAL</b>
                <input type="number" class="form-control" value="{{ $total }}" readonly id="total_a_pagar_importe">
            </th>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-sm w-100 btn-dark" onclick="muestraDatosFactura()">FACTURAR</button>
    </div>
</div>
<hr>
{{-- <div id="bloqueDatosFactura" style="display: none"> --}}
<div id="bloqueDatosFactura" style="display: none">
    <h3>DATOS PARA FACTURA</h3>
    <form id="formularioGeneraFactura">
        <div class="row">
            <div class="col-md-4">
                <label for="">M. Pago</label>
                <select name="facturacion_datos_tipo_metodo_pago" id="facturacion_datos_tipo_metodo_pago" class="form-control" required>
                    @foreach($tipoMetodoPago as $key => $value)
                    <option value="{{ $value->tipo_clasificador }}" {{ ($value->tipo_clasificador == "1")? 'selected' :'' }}>{{ $value->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="">Tipo Moneda</label>
                <select name="facturacion_datos_tipo_moneda" id="facturacion_datos_tipo_moneda" class="form-control" required>
                    @foreach($tipoMonedas as $key => $value)
                    <option value="{{ $value->tipo_clasificador }}" {{ ($value->tipo_clasificador == "1")? 'selected' :'' }}>{{ $value->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="">Tipo Documento</label>
                <select name="tipo_documento" id="tipo_documento" class="form-control" onchange="verificaNit()" required>
                    <option value="">SELECCIONE</option>
                    @foreach ($tipoDocumento as $te)
                        {{-- <option value="{{ $te->tipo_clasificador }}" {{ ($te->tipo_clasificador == 5)? 'selected' : '' }} >{{ $te->descripcion }}</option> --}}
                        <option value="{{ $te->tipo_clasificador }}">{{ $te->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="">Nit/Cedula</label>
                <input type="number" class="form-control" id="nit_factura" name="nit_factura" onchange="verificaNit()" autocomplete="off" required value="{{ $nit }}">
                <small style="display: none;" class="text-danger" id="nitnoexiste">** NIT INVALIDO **</small>
                <small style="display: none;" class="text-success" id="nitsiexiste">** NIT VALIDO **</small>
                <small style="display: none;" class="text-danger" id="errorValidar">ERROR LA VALIDAR</small>
            </div>
            <div class="col-md-3">
                <label for="">Razon Social</label>
                <input type="text" class="form-control" id="razon_factura" name="razon_factura" autocomplete="off" required value="{{ $razon_social }}">
            </div>
            <div class="col-md-2">
                <label for="">Tipo Factura</label>
                <select name="tipo_facturacion" id="tipo_facturacion" class="form-control" onchange="bloqueCAFC()">
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
                <input type="number" class="form-control" id="numero_factura_cafc" name="numero_factura_cafc">
            </div>
        </div>
        {{-- <h3 class="text-center text-info">PAGO</h3> --}}
        <div class="row" style="display: none" id="bloque_exepcion">
        {{-- <div class="row" id="bloque_exepcion"> --}}
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Enviar con execpcion?</label>
                    <input type="checkbox" name="execpcion" id="execpcion" required readonly>
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
<script>
    $(document).ready(function() {
        $('#kt_table_detalles').DataTable({
            // lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
            language: {
                // paginate: {
                //     first : 'Primero',
                //     last : 'Último',
                //     next : 'Siguiente',
                //     previous: 'Anterior'
                // },
                // search : 'Buscar:',
                // lengthMenu: 'Mostrar _MENU_ registros por página',
                // info : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order       : [],
            searching   : false,
            responsive  : true,
            lengthChange: false,
            info        : false,
            paginate    : false
        });

        $("#facturacion_datos_tipo_metodo_pago, #facturacion_datos_tipo_moneda").select2();

         // Agregar un evento para verificar el radio seleccionado al cambiar
        $('input[name="uso_cafc"]').on('change', function() {
            verificarRadioSeleccionado();
        });

    });

    function verificarRadioSeleccionado() {
        var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();
        if (valorSeleccionado === 'No') {

            $('#numero_fac_cafc').hide('toggle');
            $('#numero_factura_cafc').val(0)



            /*
            $.ajax({
                url: "{{ url('factura/sacaNumeroFactura') }}",
                method: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.estado === "success"){
                        $("#numero_factura").val(data.numero);
                        $("#codigo_cafc_contingencia").val("");
                    }else{
                        Swal.fire({
                            icon:   'error',
                            title:  'Error!',
                            text:   "Algo fallo"
                        })
                    }
                }
            })
            */
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
</script>
