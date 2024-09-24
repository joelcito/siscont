<div style="overflow-x: auto;">
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
                        <button class="btn btn-sm btn-success btn-icon" onclick="escogerCliente('{{ $cli->id }}', '{{ $cli->nombres }}', '{{ $cli->ap_paterno }}', '{{ $cli->ap_materno }}', '{{ $cli->cedula }}', '{{ $cli->nit }}', '{{ $cli->razon_social }}')"><i class="fa fa-dollar"></i></button>
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>
