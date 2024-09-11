<div class="row">
    <div class="col-3">
        <button class="btn btn-dark btn-sm" id="botonSincronizarPuntoVenta" onclick="sincronizarPuntosVentas({{ $sucursal_id }})"><i id="iconoRefreshSincronizarPuntoVenta" class="fa fa-refresh"></i> Sincronizar Punto de Venta</button>
    </div>
    <div class="col-3">
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoPuntoVenta()"><i class="fa fa-plus"></i> Nuevo Punto Venta</button>
    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_punto_ventas">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-50px">Cod. P. Venta</th>
            <th class="min-w-125px">Nombre P. Venta</th>
            <th class="min-w-125px">Tipo P. Venta</th>
            <th class="min-w-125px">Ambiente</th>
            <th class="min-w-125px">Cod CUIS</th>
            <th class="min-w-125px">Vigencia</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $punto_ventas as $pv)
            @php
                $cuis = \App\Models\Cuis::where('punto_venta_id', $pv->id)
                                        ->where('sucursal_id', $pv->sucursal_id)
                                        ->latest() // Ordena los resultados por fecha de creación en orden descendente
                                        ->first();
            @endphp
            <tr>
                <td>{{ $pv->codigoPuntoVenta }}</td>
                <td>{{ $pv->nombrePuntoVenta }}</td>
                <td>{{ $pv->tipoPuntoVenta }}</td>
                <td>
                    @if ($pv->codigo_ambiente == 1)
                        <span class="badge badge-success">PRODUCCION</span>
                    @else
                        <span class="badge badge-warning">DESARROLLO</span>
                    @endif
                </td>
                <td>
                    @if($cuis)
                        {{ $cuis->codigo }}
                    @endif
                </td>
                <td>
                    @if($cuis)
                        {{ $cuis->fechaVigencia }}
                    @endif
                </td>
                <td>
                    <button class="btn btn-icon btn-sm btn-primary" onclick="modal_genera_cuis('{{ $pv->id }}', '{{ $pv->sucursal_id }}')"><i class="fa fa-refresh"></i></button>
                    <button class="btn btn-icon btn-sm btn-dark" onclick="ajaxListadoActiviadesEconomicas('{{ $pv->id }}', '{{ $pv->sucursal_id }}')"><i class="fa fa-hat-wizard"></i></button>
                    <button class="btn btn-icon btn-sm btn-info" onclick="ajaxListadoSiatProductosServicios('{{ $pv->id }}', '{{ $pv->sucursal_id }}')"><i class="fa fa-header"></i></button>
                </td>
            </tr>
        @empty
            <h4 class="text-danger text-center">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->

<script>
    $(document).ready(function() {
        $('#kt_table_punto_ventas').DataTable({
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
