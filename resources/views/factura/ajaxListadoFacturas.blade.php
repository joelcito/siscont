<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-8 gy-2" id="kt_table_facturas">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Numero</th>
                <th>Sector</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Estado SIAT</th>
                <th>Emision</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $facturas as $fac)
                <tr>
                    {{-- <td>{{ $fac->cliente->nombres." ".$fac->cliente->ap_paterno." ".$fac->cliente->ap_materno }}</td> --}}
                    <td>{{ $fac->nombres." ".$fac->ap_paterno." ".$fac->ap_materno }}</td>
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
                        @if ($fac->siat_tipo_documento_sector->codigo_clasificador == "8")
                            Fac. Tasa Cero
                        @else
                            Fac. Com. Venta
                        @endif
                    </td>
                    <td>
                        {{ $fac->usuarioCreador->nombres." ".$fac->usuarioCreador->ap_paterno }}
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
                            <span class="badge badge-info">NO VALIDADA</span>
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
                        @if ($fac->uso_cafc == 'Si')
                            {{--  <a href="{{ $fac->empresa->url_verifica."?nit=".$fac->empresa->nit."&cuf=".$fac->cuf."&numero=".$fac->numero_cafc."&t=2" }}" target="_blank" class="btn btn-icon btn-sm tamanio_boton"><img src="{{ asset('assets/img/siat.png') }}" style="width: 25px;" alt=""></a>  --}}
                            <a href="{{ $url_verifica_factura."?nit=".$fac->empresa->nit."&cuf=".$fac->cuf."&numero=".$fac->numero_cafc."&t=2" }}" target="_blank" class="btn btn-icon btn-sm tamanio_boton"><img src="{{ asset('assets/img/siat.png') }}" style="width: 25px;" alt=""></a>
                        @else
                            {{--  <a href="{{ $fac->empresa->url_verifica."?nit=".$fac->empresa->nit."&cuf=".$fac->cuf."&numero=".$fac->numero_factura."&t=2" }}" target="_blank" class="btn btn-icon btn-sm tamanio_boton"><img src="{{ asset('assets/img/siat.png') }}" style="width: 25px;" alt=""></a>  --}}
                            <a href="{{ $url_verifica_factura."?nit=".$fac->empresa->nit."&cuf=".$fac->cuf."&numero=".$fac->numero_factura."&t=2" }}" target="_blank" class="btn btn-icon btn-sm tamanio_boton"><img src="{{ asset('assets/img/siat.png') }}" style="width: 25px;" alt=""></a>
                        @endif

                        <a  class="btn btn-primary btn-icon btn-sm tamanio_boton" title="Imprime Factura oficio" href="{{ url('factura/generaPdfFacturaNewCv', [$fac->id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                        <a  class="btn btn-white btn-icon btn-sm tamanio_boton" title="Imprime Factura rollo" href="{{ url('factura/imprimeFactura', [$fac->id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>

                        @if (is_null($fac->estado))
                            @if ($fac->tipo_factura == "offline" && is_null($fac->codigo_descripcion) )
                                <button class="btn btn-info btn-icon btn-sm tamanio_boton" onclick="modalRecepcionFacuraContingenciaFueraLinea()"><i class="fa fa-upload" aria-hidden="true"></i></button>
                            @else

                            @endif
                            <button class="btn btn-danger btn-sm btn-icon tamanio_boton" onclick="modalAnularFactura('{{ $fac->id }}')"><i class="fa fa-trash"></i></button>
                        @elseif($fac->estado == "Anulado")
                            <button class="btn btn-warning btn-sm btn-icon tamanio_boton" onclick="desanularFacturaAnulado('{{ $fac->id }}')"  title="Desanular Factura"><i class="fa fa-toggle-off"></i></button>
                        @else

                        @endif
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
            $('#kt_table_facturas').DataTable({
                lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
                // dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
                dom: 't<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
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
