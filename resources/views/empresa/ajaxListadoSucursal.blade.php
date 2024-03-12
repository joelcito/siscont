<div class="row">
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoSucursal()"><i class="fa fa-plus"></i> Nuevo Sucursal</button>
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Nombre</th>
            <th class="min-w-125px">Codigo Sucursal</th>
            <th class="min-w-125px">Direccion</th>
            <th class="text-end min-w-100px">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $sucursales as $s)
            <tr>
                <td>{{ $s->nombre }}</td>
                <td>{{ $s->codigo_sucursal }}</td>
                <td>{{ $s->direccion }}</td>
                <td>
                    {{-- <a class="btn btn-sm btn-info btn-icon" title="Configuraciones de la Empresa" href="{{ url('empresa/detalle', [$e->id]) }}"><i class="fa fa-eye"></i></a> --}}
                    <button class="btn btn-sm btn-info btn-icon" title="Puntos de Venta" onclick="modalPuntoVentas('{{ $s->id }}', '{{ $s->nombre }}')"><i class="fa fa-home"></i></button>
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
