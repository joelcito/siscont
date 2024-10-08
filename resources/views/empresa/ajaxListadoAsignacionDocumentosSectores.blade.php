<div class="row">
    <div class="col-3">
        @if ($isAdmin)
            <button class="btn btn-primary btn-sm" onclick="modalNuevoAginacionDocumentoSector()"><i class="fa fa-plus"></i> Nuevo Documento Sector</button>
        @endif
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div>
<table class="table align-middle table-row-dashed fs-6 gy-5" id="table_documentos_sectores">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Empresa</th>
            <th class="min-w-125px">Documento Sector</th>
            @if ($isAdmin)
            <th class="text-end min-w-100px">Actions</th>
            @endif
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $documentos_sectores_asignados as $dsa)
            <tr>
                <td>{{ $dsa->empresa->nombre }}</td>
                <td>{{ $dsa->siat_tipo_documento_sector->descripcion }}</td>
                @if ($isAdmin)
                <td>
                    <button class="btn btn-sm btn-danger btn-icon" onclick="eliminarAsignaconDocumentoSector('{{ $dsa->id }}')"><i class="fa fa-trash"></i></button>
                </td>
                @endif
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<script>
    $(document).ready(function() {
            $('#table_documentos_sectores').DataTable({
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
