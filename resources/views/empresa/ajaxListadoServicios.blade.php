<div class="row">
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoServicio()"><i class="fa fa-plus"></i> Nuevo Servicio</button>
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_servicios">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Actividad Economica Siat</th>
            <th>Servicio Siat</th>
            <th>Unidad Medida Siat</th>
            <th>Descripcion</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $servicios as $s)
            <tr>
                <td>{{ $s->siatDependeActividad->descripcion }}</td>
                <td>{{ $s->siatProductoServicio->descripcion_producto }}</td>
                <td>{{ $s->siatUnidadMedida->descripcion }}</td>
                <td>{{ $s->descripcion }}</td>
                <td>{{ $s->precio }}</td>
                <td>
                    <button class="btn btn-warning btn-icon btn-sm" onclick="editaraSErvicio('{{ $s->id }}', '{{ $s->siat_documento_sector_id }}','{{ $s->siat_depende_actividades_id }}','{{ $s->siat_producto_servicios_id }}','{{ $s->siat_unidad_medidas_id }}','{{ $s->numero_serie }}','{{ $s->codigo_imei }}','{{ $s->descripcion }}','{{ $s->precio }}')"><i class="fa fa-edit"></i></button>
                    @if ($s->detalles($s->id) == "0")
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarServicio('{{ $s->id }}')"><i class="fa fa-trash"></i></button>
                    @endif
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
        $('#kt_table_servicios').DataTable({
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
