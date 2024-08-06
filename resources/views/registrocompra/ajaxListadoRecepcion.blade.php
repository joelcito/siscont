<!--begin::Table-->
<form id="formulario_facturo_compra_a_enviar">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Id</th>
                <th class="min-w-125px">Nit</th>
                <th class="min-w-125px">Razon Social</th>
                <th class="min-w-125px">Monto</th>
                <th class="min-w-125px">Fecha</th>
                <th class="">Envio</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $facturas as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->nit }}</td>
                    <td>{{ $r->razon_social }}</td>
                    <td>{{ $r->total }}</td>
                    <td>{{ $r->fecha }}</td>
                    <td>
                        <input type="checkbox" name="factura_{{ $r->id }}" id="factura_{{ $r->id }}" checked>
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
</form>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-dark btn-sm w-100" onclick="envioPaquetesFacturasCompra()">Enviar Facturas</button>
    </div>
</div>
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
