<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_eventos_significativos">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th >Evento Significativo</th>
            <th >Cufd Vigente</th>
            <th >Cufd Evento</th>
            <th >Descripcion</th>
            <th >Fecha Inicio</th>
            <th >Fecha Fin</th>
            <th >Codigo Recepcion</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $eventosSignificativos as $es)
            <tr>
                <td>{{ $es->siat_evento->descripcion }}</td>
                <td >{{ $es->cufd_activo->fecha_vigencia." | ".$es->cufd_activo->codigo_control }}</td>
                <td >{{ $es->cufd_antiguo->fecha_vigencia." | ".$es->cufd_antiguo->codigo_control }}</td>
                <td >{{ $es->descripcion }}</td>
                <td>{{ $es->fecha_ini_evento }}</td>
                <td>{{ $es->fecha_fin_evento }}</td>
                <td>{{ $es->codigoRecepcionEventoSignificativo }}</td>
                <td>
                    {{-- <button class="btn btn-icon btn-sm btn-warning"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-icon btn-sm btn-danger"><i class="fa fa-trash"></i></button> --}}
                    {{-- <a class="btn btn-sm btn-info btn-icon" title="Configuraciones de la Empresa" href="{{ url('empresa/detalle', [$e->id]) }}"><i class="fa fa-eye"></i></a> --}}
                </td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->

<script>
    $(document).ready(function() {
            $('#kt_table_eventos_significativos').DataTable({
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
