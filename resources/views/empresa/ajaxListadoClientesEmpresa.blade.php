<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_clientes">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Nombres</th>
            <th>Ap Paterno</th>
            <th>Ap Materno</th>
            <th>Celular</th>
            <th>Nit</th>
            <th>Razon Social</th>
            <th>Cedula</th>
            <th>Correo</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $clientes as $cli)
            <tr>
                <td>{{ $cli->nombres }}</td>
                <td>{{ $cli->ap_paterno }}</td>
                <td>{{ $cli->ap_materno }}</td>
                <td>{{ $cli->numero_celular }}</td>
                <td>{{ $cli->nit }}</td>
                <td>{{ $cli->razon_social }}</td>
                <td>{{ $cli->cedula }}</td>
                <td>{{ $cli->correo }}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-icon"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-icon"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<script>
    $(document).ready(function() {
            $('#kt_table_clientes').DataTable({
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
