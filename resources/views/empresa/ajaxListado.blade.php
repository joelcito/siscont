<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th >Logo</th>
            <th >Nombre</th>
            <th >Nit</th>
            <th >Razon Social</th>
            <th >Ambiente</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $empresas as $e)
            <tr>
                <td>
                    <div style="width: 50px;" align="center">
                        @if ($e->logo != null)
                            <img src="{{ asset('assets/img')."/".$e->logo }}" width="100%" alt="">
                        @else
                            <img src="{{ asset('assets/img/default.jpg') }}" width="100%" alt="">
                        @endif
                    </div>
                </td>
                <td>{{ $e->nombre }}</td>
                <td>{{ $e->nit }}</td>
                <td>{{ $e->razon_social }}</td>
                <td>
                    @if ($e->codigo_ambiente === "2")
                        <span class="badge badge-warning">DESARROLLO</span>
                    @else
                        <span class="badge badge-success">PRODUCCION</span>
                    @endif
                </td>
                <td>
                    <a class="btn btn-sm btn-info btn-icon" title="Detalles de la Empresa" href="{{ url('empresa/detalle', [$e->id]) }}"><i class="fa fa-eye"></i></a>
                    @if (count($e->facturas) == 0 || $e->codigo_ambiente == "2")
                        <button class="btn btn-sm btn-icon btn-danger" title="Eliminar Empresa" onclick="eliminarEmpresa('{{ $e->id }}')"><i class="fa fa-trash"></i></button>
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
