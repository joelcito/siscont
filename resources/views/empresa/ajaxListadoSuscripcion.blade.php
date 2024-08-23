<div class="row">
    <div class="col-3">
        {{-- <button class="btn btn-dark btn-sm" onclick="sincronizarPuntosVentas()"><i class="fa fa-refresh"></i> Sincronizar Punto de Venta</button> --}}
        {{-- <button class="btn btn-dark btn-sm" onclick="sincronizarPuntosVentas({{ $sucursal_id }})"><i class="fa fa-refresh"></i> Sincronizar Punto de Venta</button> --}}
    </div>
    <div class="col-3">
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoSuscripcion()"><i class="fa fa-plus"></i> Nuevo Suscripcion</button>
    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_suscripciones">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th >Empresa</th>
            <th >Plan</th>
            <th >Tipo de Plan</th>
            <th >Precio</th>
            <th >Cant. Fact.</th>
            <th >Fecha Inicio</th>
            <th >Fecha Fin</th>
            <th >Descripcion</th>
            <th >Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $suscripciones as $sus)
            <tr>
                <td>{{ $sus->empresa->nombre }}</td>
                <td>{{ $sus->plan->nombre }}</td>
                <td>{{ $sus->plan->tipo_plan }}</td>
                <td>{{ $sus->plan->precio }}</td>
                <td>{{ $sus->plan->cantidad_factura }}</td>
                <td>{{ $sus->fecha_inicio }}</td>
                <td>{{ $sus->fecha_fin }}</td>
                <td>{{ $sus->descripcion }}</td>
                <td>

                </td>
                <td>
                    {{-- <button class="btn btn-icon btn-sm btn-primary" onclick="modal_genera_cuis('{{ $sus->id }}', '{{ $sus->sucursal_id }}')"><i class="fa fa-refresh"></i></button>
                    <button class="btn btn-icon btn-sm btn-dark" onclick="ajaxListadoActiviadesEconomicas('{{ $sus->id }}', '{{ $sus->sucursal_id }}')"><i class="fa fa-hat-wizard"></i></button>
                    <button class="btn btn-icon btn-sm btn-info" onclick="ajaxListadoSiatProductosServicios('{{ $sus->id }}', '{{ $sus->sucursal_id }}')"><i class="fa fa-header"></i></button> --}}
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
        $('#kt_table_suscripciones').DataTable({
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
