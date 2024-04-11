{{-- <div class="row">
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoCliente()"><i class="fa fa-plus"></i> Nuevo Cliente</button>
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div> --}}
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
                <td>{{ $det->servicio->descripcion }}</td>
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
        <button class="btn btn-sm w-100 btn-dark">FACTURAR</button>
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

    });
</script>
