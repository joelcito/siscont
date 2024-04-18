<div class="row">
    <div class="col-md-12">
        <button class="btn w-100 btn-sm btn-primary" onclick="sincronizarTipoMoneda()">Sincronizar</button>
    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="table_tipo_moneda">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Id</th>
            <th class="min-w-125px">Codigo Clasificador</th>
            <th class="min-w-125px">Descripcion</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $tipoMonedas as $tm)
            <tr>
                <td>{{ $tm->id }}</td>
                <td>{{ $tm->tipo_clasificador }}</td>
                <td>{{ $tm->descripcion }}</td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->

<script>
    $(document).ready(function() {
        $('#table_tipo_moneda').DataTable({
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
