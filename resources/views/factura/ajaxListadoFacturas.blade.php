{{-- <div class="row">
    <div class="col-3">
        <button class="btn btn-primary btn-sm" onclick="modalNuevoCliente()"><i class="fa fa-plus"></i> Nuevo Cliente</button>
    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
    <div class="col-3">

    </div>
</div> --}}
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_facturas">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Numero</th>
            <th>Estado</th>
            <th>Estado SIAT</th>
            <th>Emision</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $facturas as $fac)
            <tr>
                <td>{{ $fac->cliente->nombres." ".$fac->cliente->ap_paterno." ".$fac->cliente->ap_materno }}</td>
                <td>{{ $fac->fecha }}</td>
                <td>{{ $fac->total }}</td>
                <td>
                    @if ($fac->uso_cafc == "Si")
                        <span class="text-primary">N° Cafc: </span>{{ $fac->numero_cafc }}
                    @else
                        {{ $fac->numero_factura }}
                    @endif
                </td>
                <td>
                    @if (is_null($fac->estado))
                        <span class="badge badge-success">VIGENTE</span>
                    @elseif($fac->estado == "Anulado")
                        <span class="badge badge-danger">ANULADO</span>
                    @endif
                </td>
                <td>
                    @if ($fac->codigo_descripcion == "VALIDADA")
                        <span class="badge badge-success">{{ $fac->codigo_descripcion }}</span>
                    @elseif($fac->codigo_descripcion == "OBSERVADA")
                        <span class="badge badge-danger">{{ $fac->codigo_descripcion }}</span>
                    @elseif($fac->codigo_descripcion == "PENDIENTE")
                        <span class="badge badge-warning">{{ $fac->codigo_descripcion }}</span>
                    @else
                        <span class="badge badge-info">PARA SU ENVIO FUERA DE LINEA 1</span>
                    @endif
                </td>
                <td>
                    @if ($fac->tipo_factura == "online")
                        <span class="badge badge-success">ONLINE</span>
                    @elseif($fac->tipo_factura == "offline")
                        <span class="badge badge-warning">OFFLINE</span>
                    @else
                        {{--  algo aqui :>  --}}
                        {{--  <span class="badge badge-info">PARA SU ENVIO FUERA DE LINEA 2</span>  --}}
                    @endif
                </td>
                <td>
                    {{-- <a href="https://siat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf={{ $p->cuf }}&numero={{ $p->numero_cafc }}&t=2" target="_blank" class="btn btn-dark btn-icon btn-sm"><i class="fa fa-file"></i></a> --}}
                    <a href="{{ $fac->empresa->url_verifica."?nit=".$fac->nit."&cuf=".$fac->cuf."&numero=".$fac->numero_factura."&t=2" }}" target="_blank" class="btn btn-dark btn-icon btn-sm"><i class="fa fa-file"></i></a>

                    @if (is_null($fac->estado))
                        @if ($fac->tipo_factura == "offline" && is_null($fac->codigo_descripcion) )
                            <button class="btn btn-info btn-icon btn-sm" onclick="modalRecepcionFacuraContingenciaFueraLinea()"><i class="fa fa-upload" aria-hidden="true"></i></button>
                        @else

                        @endif
                        <button class="btn btn-danger btn-sm btn-icon" onclick="modalAnularFactura('{{ $fac->id }}')"><i class="fa fa-trash"></i></button>
                    @elseif($fac->estado == "Anulado")
                        <button class="btn btn-warning btn-sm btn-icon" onclick="desanularFacturaAnulado('{{ $fac->id }}')"  title="Desanular Factura"><i class="fa fa-toggle-off"></i></button>
                    @else

                    @endif
                    {{-- <button class="btn btn-sm btn-info btn-icon" title="Puntos de Venta" onclick="modalPuntoVentas('{{ $fac->id }}', '{{ $fac->nombre }}', {{ $fac->codigo_sucursal }})"><i class="fa fa-home"></i></button>
                    <button class="btn btn-sm btn-warning btn-icon"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-icon"><i class="fa fa-trash"></i></button> --}}
                    {{-- <button class="btn btn-sm btn-success btn-icon" onclick="escogerCliente('{{ $fac->id }}', '{{ $fac->nombres }}', '{{ $fac->ap_paterno }}', '{{ $fac->ap_materno }}', '{{ $fac->cedula }}')"><i class="fa fa-dollar"></i></button> --}}
                </td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
    </tbody>
</table>
<script>
    $(document).ready(function() {
            $('#kt_table_facturas').DataTable({
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
