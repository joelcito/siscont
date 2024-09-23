<div style="overflow-x: auto;">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Documento Sector</th>
                <th>Ambiente</th>
                <th>Modalidad</th>
                <th>Nombre</th>
                <th>URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $url_apis_servicios as $urls)
                <tr>
                    <td>
                        @if ($urls->siat_tipo_documento_sector)
                            {{ $urls->siat_tipo_documento_sector->descripcion }}
                        @endif
                    </td>
                    <td>
                        @if ($urls->ambiente === "2")
                            <span class="badge badge-warning">DESARROLLO</span>
                        @else
                            <span class="badge badge-success">PRODUCCION</span>
                        @endif
                    </td>
                    <td>
                        @if ($urls->modalidad === "1")
                            <span class="badge badge-primary">ELECTRÓNICA EN LÍNEA</span>
                        @else
                            <span class="badge badge-info">COMPUTARIZADA EN LÍNEA</span>
                        @endif
                    </td>
                    <td>{{ $urls->nombre }}</td>
                    <td>{{ $urls->url_servicio }}</td>
                    <td>

                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
    <!--end::Table-->
</div>

<script>
    $(document).ready(function() {
            $('#kt_table_users').DataTable({
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
