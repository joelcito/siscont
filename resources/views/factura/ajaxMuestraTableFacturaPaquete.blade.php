<div class="row">
    <div class="col-md-12">
        @if (count($facturas) > 0)
            <button class="btn btn-dark btn-sm w-100" onclick="mandarFacturasPaquete()">Enviar Paquete de Facturas</button>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <form action="" id="formularioEnvioPaquete">
            <table class="tablesaw table-striped table-hover table-bordered table no-wrap" id="tablaPaqute">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIT</th>
                        <th>RAZON SOCIAL</th>
                        <th>FECHA DE EMISION</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($facturas as $f)
                        <tr>
                            <td>{{ $f->id }}</td>
                            <td>{{ $f->nit }}</td>
                            <td>{{ $f->razon_social." | ".$f->codigo_descripcion }}</td>
                            <td>{{ $f->fecha }}</td>
                            <td>
                                {{--  <input type="checkbox" name="check_{{ $f->id }}">  --}}
                                <input type="checkbox" checked name="check_{{ $f->id }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>
