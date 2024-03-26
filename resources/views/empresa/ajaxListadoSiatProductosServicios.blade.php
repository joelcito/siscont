<div class="row">
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="sincronizarSiatProductoServicios('{{$punto_venta}}', '{{$sucursal}}')"><i class="fa fa-plus"></i> Sincronizar</button>
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siat_producto_servicios">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Sucursal</th>
            <th>P. Venta</th>
            <th>Ambiente</th>
            <th>Cod. Actividad</th>
            <th>Cod. Producto</th>
            <th>Descripcion</th>
            <th>Nandina Pri</th>
            <th>Nandina Seg</th>
            {{-- <th>Acciones</th> --}}
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $siatProductosServicios as $sps)
            <tr>
                <td>{{ $sps->sucursal->nombre }}</td>
                <td>{{ $sps->puntoVenta->nombrePuntoVenta }}</td>
                <td>
                    @if ($sps->codigo_ambiente ==  1)
                        <span class="badge badge-success">PRODUCCION</span>
                    @else
                        <span class="badge badge-warning">DESARROLLO</span>
                    @endif
                </td>
                <td>{{ $sps->codigo_actividad }}</td>
                <td>{{ $sps->codigo_producto }}</td>
                <td>{{ $sps->descripcion_producto }}</td>
                <td>{{ $sps->nandina_pri }}</td>
                <td>{{ $sps->nandina_seg }}</td>
                {{-- <td></td> --}}
            </tr>
        @empty
            <h4 class="text-danger text-center">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->

<script>
    $(document).ready(function() {
        $('#kt_table_siat_producto_servicios').DataTable({
            lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
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
            //  searching: true,
            responsive: true
        });
    });


</script>
