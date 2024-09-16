<div class="row">
    <div class="col-md-3">
        <button class="btn w-100 btn-sm btn-primary" onclick="agregarUsuarioEmpresa()">Agregar Usuario</button>
    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="table_punto_venta">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Nombres</th>
            <th>Ap Paterno</th>
            <th>Ap Materno</th>
            <th>Numero Celular</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Accion</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $usuarios as $u)
            <tr>
                <td>{{ $u->nombres }}</td>
                <td>{{ $u->ap_paterno }}</td>
                <td>{{ $u->ap_materno }}</td>
                <td>{{ $u->numero_celular }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->rol->nombres }}</td>
                <td>{{ $u->estado }}</td>
                <td>
                    <button class="btn btn-warning btn-sm btn-icon" onclick="editarUsuario('{{ $u->id }}','{{ $u->nombres }}','{{ $u->ap_paterno }}','{{ $u->ap_materno }}','{{ $u->numero_celular }}','{{ $u->email }}','{{ $u->rol->id }}','{{ $u->sucursal->id }}','{{ $u->puntoVenta->id }}')"><i class="fa fa-edit"></i></button>
                    @if ($u->contarFacturas() == 0)
                        <button class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                    @endif
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
        $('#table_punto_venta').DataTable({
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
